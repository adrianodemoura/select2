<?php
/**
 * Class S2Form
 *
 * @package     Select2.View.Helper
 * @author      Adriano Moura
 */
App::uses('Select2AppHelper', 'Select2.View/Helper');
/**
 * Retorna um elemento select2.
 *
 * @example
 *
 * $this->Helpers->load('Select2.S2Form');
 *
 * echo $this->S2Form->input('Model.campo', ['label'=>false, width'=>'resolve', 'style'=>'min-width: 200px;', 'ajax'=>['url'=>'http://site.com.br/cadastro/get_lista']]);
 *
 */
class S2FormHelper extends Select2AppHelper {
    /**
     * Ajudantes
     *
     * @var     array
     */
    public $helpers = ['Form', 'Html'];

    /**
     * Elementos que vão usar o select2.
     *
     * @var     array
     */
    private $elements = [];

    /**
     * Executa chamada antes da renderização da view.
     *
     *
     * @param   string  $viewFile   O Arquivo que será renderizado.
     * @return  void
     */
    public function afterRender($viewFile)
    {
        if (!empty($this->elements))
        {
            // incluindo o JS e CSS do select2.
            echo $this->Html->script(['/select2/js/select2.min', '/select2/js/select2_pt-BR', '/select2/js/select2_app'],  ['inline'=>false]);
            echo $this->Html->css(['/select2/css/select2.min'], ['inline'=>false]);

            // iniciando o jquery
            $htmlScript = "$(document).ready(function() {\n";
            foreach ($this->elements as $_field => $_params)
            {
                $htmlScript .= "\t$('#".$_field."').select2(\n\t".json_encode($_params, JSON_FORCE_OBJECT);
                $htmlScript .= ");\n";
            }
            $htmlScript .= "\n});\n";

            // tornando a função legível para o jquery.
            $htmlScript = str_replace('"function(resposta)','function(resposta)', $htmlScript);
            $htmlScript = str_replace('}"}})','}}})', $htmlScript);

            // escrevendo o jsScript
            echo $this->Html->scriptBlock($htmlScript, ['inline'=>false]);
        }
    }

    /**
     * Retorna o elemento select2
     *
     * @param   string  $field      Nome do campo, no formato Model.Field
     * @param   array   $params     Parâmetros do elemento input, incrementando com os parâmetros do elemento select2.
     * @return  string  $html       Elemento select.
     */
    public function input($field='', $params=[])
    {
        $html = "";

        try 
        {
            // Nome do campo obrigatório.
            if (empty($field))
            {
                throw new Exception(__('id inválido !'), 1);
            }

            // configurando o id conforme o cake.
            $id = $this->Form->domId($field);

            // Propriedades default do select2.
            $paramsSelect2['width']             = isset($params['width'])                 ? $params['width']                : 'resolve';
            $paramsSelect2['language']          = isset($params['language'])              ? $params['language']             : 'pt-BR';
            $paramsSelect2['placeholder']       = isset($params['placeholder'])           ? $params['placeholder']          : $id;
            $paramsSelect2['minimumInputLength']= isset($params['minimumInputLength'])    ? $params['minimumInputLength']   : 3;
            $funcaoTrataRespostaSelect2         = isset($params['customFunctionResponse'])? $params['customFunctionResponse']   : 'retornaRespostaSelect2';

            // Propriedades obrigatórias para o ajax.
            $paramsSelect2['ajax']['dataType']      = 'JSON';
            $paramsSelect2['ajax']['method']        = 'POST';
            $paramsSelect2['ajax']['delay']         = isset($params['ajax']['delay'])  ? $params['ajax']['delay']  : 150;
            $paramsSelect2['ajax']['url']           = isset($params['ajax']['url'])    ? $params['ajax']['url']    : Router::url('/',true).$this->request->controller.'/get_lista_select2';
            $paramsSelect2['ajax']['processResults']= "function(resposta) { return $funcaoTrataRespostaSelect2(resposta); }";

            // incrementando os elementos select2.
            $this->elements[$id] = $paramsSelect2;

            // Removendo propriedades do select2 para não sujar o elemento.
            unset($params['width']);
            unset($params['language']);
            unset($params['customFunctionResponse']);
            unset($params['minimumInputLength']);
            unset($params['ajax']);

            // Obrigando o tipo select.
            $params['type'] = 'select';

            $html = $this->Form->input($field, $params);    
        } catch (Exception $e)
        {
            $html = __('Erro ao tentar montar elemento select2: ').$e->getMessage();
        }
        

        return $html;
    }
}
