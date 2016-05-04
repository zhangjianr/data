// JavaScript Document
/**
 * 点选可选属性或改变数量时修改商品价格的函数
 */
function changePrice()
{
  var attr = getSelectedAttributes(document.forms['addtocart_form']);
  
  var qty = document.forms['addtocart_form'].elements['Qty'].value;
  var no_attr_price  =  document.forms['addtocart_form'].elements['no_attr_price'].value;
  var param = {action:'price',no_attr_price:no_attr_price,'ProductsID':Products_ID,'attr':attr,'qty':qty,'UsersID':UsersID};
  
  $("#spec_list").attr('value',attr);
  $.get(base_url+'api/shop/cart/ajax.php',param,function(data){
  		 if (data.status = 0)
 		 {
    		alert(data.mgs);
  		 }
  		 else
  		 {
			$("#cur_price").attr('value',data.result);
			$("#cur-price-txt").html(formatCurrency(parseFloat(data.result)*qty));
		 }
		 
  },'json');
}


function changeAtt(t) {
    var name = $(t).attr('name');
	$('#spec_value_'+name).prop('checked',true);
	
	t.lastChild.checked='checked';
	for (var i = 0; i<t.parentNode.childNodes.length;i++) {
			if (t.parentNode.childNodes[i].className == 'cattsel') {
				t.parentNode.childNodes[i].className = '';
			}
		}
	t.className = "cattsel";
	changePrice();
}

/**

 * 获得选定的商品属性

 */

function getSelectedAttributes(formBuy)

{

  var spec_arr = new Array();

  var j = 0;

  for (i = 0; i < formBuy.elements.length; i ++ )

  {

    var prefix = formBuy.elements[i].name.substr(0, 5);



    if (prefix == 'spec_' && (

      ((formBuy.elements[i].type == 'radio' || formBuy.elements[i].type == 'checkbox') && formBuy.elements[i].checked) ||

      formBuy.elements[i].tagName == 'SELECT'))

    {

      spec_arr[j] = formBuy.elements[i].value;

      j++ ;

    }

  }



  return spec_arr;

}

function mauual_check(){
 var attr_id_str =  $("#spec_list").attr('value');
 var attr_array = attr_id_str.split(',');
 

 for(var product_attr_id in attr_array){
	 $('#spec_value_'+attr_array[product_attr_id]).prop('checked',true);
 }
}


/** 
 * 将数值四舍五入(保留2位小数)后格式化成金额形式 
 * 
 * @param num 数值(Number或者String) 
 * @return 金额格式的字符串,如'1,234,567.45' 
 * @type String 
 */  
function formatCurrency(num) {  
    num = num.toString().replace(/\$|\,/g,'');  
    if(isNaN(num))  
        num = "0";  
    sign = (num == (num = Math.abs(num)));  
    num = Math.floor(num*100+0.50000000001);  
    cents = num%100;  
    num = Math.floor(num/100).toString();  
    if(cents<10)  
    cents = "0" + cents;  
    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)  
    num = num.substring(0,num.length-(4*i+3))+','+  
    num.substring(num.length-(4*i+3));  
    return (((sign)?'':'-') + num + '.' + cents);  
}  
