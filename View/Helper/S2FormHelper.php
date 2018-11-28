<?php
/**
 * Class S2Form
 *
 * @package     Select2.View.Helper
 * @author      Adriano Moura
 */
App::uses('Select2AppHelper', 'Select2.View/Helper');
/**
 * Return element select2.
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
     * Helpers
     *
     * @var     array
     */
    public $helpers = ['Form', 'Html'];

    /**
     * elements select2.
     *
     * @var     array
     */
    private $elements = [];

    /**
     * Execute call before render of the view.
     *
     * @param   string  $viewFile   file to render
     * @return  void
     */
    public function afterRender($viewFile)
    {
        if (!empty($this->elements))
        {
            echo $this->Html->script(['/select2/js/select2.min', '/select2/js/select2_pt-BR', '/select2/js/select2_app'],  ['inline'=>false]);
            echo $this->Html->css(['/select2/css/select2.min'], ['inline'=>false]);

            $htmlScript = "$(document).ready(function() {\n";
            foreach ($this->elements as $_field => $_params)
            {
                $htmlScript .= "\t$('#".$_field."').select2(\n\t".json_encode($_params, JSON_FORCE_OBJECT);
                $htmlScript .= ");\n";
            }
            $htmlScript .= "\n});\n";

            $htmlScript = str_replace('"function(resposta)','function(resposta)', $htmlScript);
            $htmlScript = str_replace('}"}})','}}})', $htmlScript);

            echo $this->Html->scriptBlock($htmlScript, ['inline'=>false]);
        }
    }

    /**
     * Return element select2
     *
     * @param   string  $field      Name field, in the format: Model.Field
     * @param   array   $params     Parameters of the input element, incrementing with the parameters of the select2 element.
     * @return  string  $html       element select.
     */
    public function input($field='', $params=[])
    {
        $html = "";

        try 
        {
            if (empty($field))
            {
                throw new Exception(__('id inválido !'), 1);
            }

            $id = $this->Form->domId($field);

            $paramsSelect2['width']             = isset($params['width'])                 ? $params['width']                : 'resolve';
            $paramsSelect2['language']          = isset($params['language'])              ? $params['language']             : 'pt-BR';
            $paramsSelect2['placeholder']       = isset($params['placeholder'])           ? $params['placeholder']          : $id;
            $paramsSelect2['minimumInputLength']= isset($params['minimumInputLength'])    ? $params['minimumInputLength']   : 3;
            $funcaoTrataRespostaSelect2         = isset($params['customFunctionResponse'])? $params['customFunctionResponse']   : 'retornaRespostaSelect2';

            $paramsSelect2['ajax']['dataType']      = 'JSON';
            $paramsSelect2['ajax']['method']        = 'POST';
            $paramsSelect2['ajax']['delay']         = isset($params['ajax']['delay'])  ? $params['ajax']['delay']  : 150;
            $paramsSelect2['ajax']['url']           = isset($params['ajax']['url'])    ? $params['ajax']['url']    : Router::url('/',true).$this->request->controller.'/get_lista_select2';
            $paramsSelect2['ajax']['processResults']= "function(resposta) { return $funcaoTrataRespostaSelect2(resposta); }";

            $this->elements[$id] = $paramsSelect2;

            unset($params['width']);
            unset($params['language']);
            unset($params['customFunctionResponse']);
            unset($params['minimumInputLength']);
            unset($params['ajax']);

            $params['type'] = 'select';

            $html = $this->Form->input($field, $params);    
        } catch (Exception $e)
        {
            $html = __('Erro ao tentar montar elemento select2: ').$e->getMessage();
        }

        return $html;
    }
}
