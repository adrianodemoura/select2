/**
 * Handle the return of element select2.
 *
 * @param 	response 	Array with response of server.
 * @return 	results 	Array in format id.text
 */
function returnResponsSelect2(response)
{
	let resultado = [{id: response.status, text: response.msg}];
	if (response.status)
	{
		resultado = $.map(response.lista, function(obj, i)
		{
			return { id: i, text: obj };
		});
	} 

	return { results: resultado };
}