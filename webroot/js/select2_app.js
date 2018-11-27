/**
 * Trata o retorno do element select2
 *
 * param 	resposta 	Matriz com os dados a serem tratados.
 * return 	results 	Mariz com o resultado tratado.
 */
function retornaRespostaSelect2(resposta)
{
	let resultado = [{id: resposta.status, text: resposta.msg}];
	if (resposta.status)
	{
		resultado = $.map(resposta.lista, function(obj, i)
		{
			return { id: i, text: obj };
		});
	} 

	return { results: resultado };
}