function loadProperties(event_id)
{
    var xmlhttp;
    xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function()
    {
	if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{
	    document.getElementById("properties").innerHTML=xmlhttp.responseText;
	}
    }
    xmlhttp.open("GET","async_properties.php?i="+event_id,true);
    xmlhttp.send();
}
