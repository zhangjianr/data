// JavaScript Document
/**
 * 新增一个规格
 */
function addSpec(obj)
{
	htmlTpl = '<tr>' + $(obj).parents("tr").html() + '</tr>';
	htmlTpl = htmlTpl.replace(/(.*)(addSpec)(.*)(\[)(\+)/i, "$1removeSpec$3$4-");
	$(obj).parents("tbody").append(htmlTpl);
}

/**
 * 删除规格值
 */
function removeSpec(obj)
{
	var row = rowindex(obj.parentNode.parentNode);
	var tbl = document.getElementById('attrTable');

	tbl.deleteRow(row);
}

/**
 * 处理规格
 */
function handleSpec()
{
	var elementCount = document.forms['theForm'].elements.length;
	for (var i = 0; i < elementCount; i++)
	{
		var element = document.forms['theForm'].elements[i];
		if (element.id.substr(0, 5) == 'spec_')
		{
			var optCount = element.options.length;
			var value = new Array(optCount);
			for (var j = 0; j < optCount; j++)
			{
				value[j] = element.options[j].value;
			}

			var hiddenSpec = document.getElementById('hidden_' + element.id);
			hiddenSpec.value = value.join(String.fromCharCode(13)); // 用回车键隔开每个规格
		}
	}
	return true;
}

