
function tmenudata0()
{


    /*---------------------------------------------
    Image Settinngs (icons and plus minus symbols)
    ---------------------------------------------*/


	this.imgage_gap = 3			//The image gap is applied to the left and right of the folder and document icons.
						//In the absence of a folder or document icon the gap is applied between the
						//plus / minus symbols and the text only.


	this.plus_image = "javascript/plus.gif"		//specifies a custom plus image.
	this.minus_image = "javascript/minus.gif"		//specifies a custom minus image.
	this.pm_width_height = "9,9"		//Width & Height  - Note: Both images must be the same dimensions.


	this.folder_image = "javascript/folder.gif"	//Automatically applies to all items which may be expanded.
	this.document_image = "javascript/document.gif"	//Automatically applies to all items which are not expandable.
	this.icon_width_height = "16,14"	//Width & Height  - Note: Both images must be the same dimensions.




    /*---------------------------------------------
    General Settings
    ---------------------------------------------*/


	this.indent = 20;			//The indent distance in pixels for each level of the tree.
	this.use_hand_cursor = true;		//Use a hand mouse cursor for expandable items, or the default arrow.




    /*---------------------------------------------
    Tree Menu Styles
    ---------------------------------------------*/


	this.main_item_styles =           "text-decoration:none;		\
                                           font-weight:normal;			\
                                           font-family:Arial;			\
                                           font-size:12px;			\
                                           color:#333333;			\
                                           padding:2px;				"


        this.sub_item_styles =            "text-decoration:none;		\
                                           font-weight:normal;			\
                                           font-family:Arial;			\
                                           font-size:12px;			\
                                           color:#333333;			"



	/* Styles may be formatted as multi-line (seen above), or on a single line as shown below.
	   The expander_hover_styles apply to menu items which expand to show child menus.*/

	this.main_container_styles = "padding:0px;"
	this.sub_container_styles = "padding-top:7px; padding-bottom:7px;"

	this.main_link_styles = "color:#0066aa; text-decoration:none;"
	this.main_link_hover_styles = "color:#ff0000; text-decoration:underline;"

	this.sub_link_styles = ""
	this.sub_link_hover_styles = ""

	this.main_expander_hover_styles = "text-decoration:underline;";
	this.sub_expander_hover_styles = "";

}

function cc5(cc9,cc10,cc2,cc11){
	eval("cc8=new Array("+cc2.pm_width_height+")");
	this.cc7=0;
	for(this.li=0;this.li<cc9.length;this.li++){
		if(cc9[this.li].tagName=="LI"){
			this.level=cc10.split("_").length-1;
			if(this.level>ulm_mlevel)ulm_mlevel=this.level;
			cc9[this.li].style.cursor="default";
			this.cc12=false;
			this.cc13=cc9[this.li].childNodes;
			for(this.ti=0;this.ti<this.cc13.length;this.ti++){
				if(this.cc13[this.ti].tagName=="UL"){
					this.usource=cc3["img"+cc11].src;
					if((gev=cc9[this.li].getAttribute("expanded"))&&(parseInt(gev))){
						this.cc13[this.ti].style.display="block";
						this.usource=cc4["img"+cc11].src;
					} else this.cc13[this.ti].style.display="none";
					if(cc2.folder_image){
						create_images(cc2,cc11,cc2.icon_width_height,cc2.folder_image,cc9[this.li]);
						this.ti=this.ti+2;}this.cc14=document.createElement("IMG");
						this.cc14.setAttribute("width",cc8[0]);
						this.cc14.setAttribute("height",cc8[1]);
						this.cc14.className="plusminus";
						this.cc14.src=this.usource;
						this.cc14.onclick=cc16;
						this.cc14.onselectstart=function(){return false};
						this.cc14.setAttribute("cc2_id",cc11);
						this.cc15=document.createElement("div");
						this.cc15.style.display="inline";
						this.cc15.style.paddingLeft=cc2.imgage_gap+"px";
						cc9[this.li].insertBefore(this.cc15,cc9[this.li].firstChild);
						cc9[this.li].insertBefore(this.cc14,cc9[this.li].firstChild);
						this.ti+=2;
						new cc5(this.cc13[this.ti].childNodes,cc10+this.cc7+"_",cc2,cc11);
						this.cc12=1;
					} else  if(this.cc13[this.ti].tagName=="SPAN"){
						this.cc13[this.ti].onselectstart=function(){return false};
						// this.cc13[this.ti].onclick=cc16;
						this.cc13[this.ti].setAttribute("cc2_id",cc11);
						this.cname="ctmmainhover";
						if(this.level>1)this.cname="ctmsubhover";
						if(this.level>1)this.cc13[this.ti].onmouseover=function(){this.className="ctmsubhover";};
						else this.cc13[this.ti].onmouseover=function(){this.className="ctmmainhover";};
						this.cc13[this.ti].onmouseout=function(){this.className="";};
					}
			}
			if(!this.cc12){
				if(cc2.document_image){
					create_images(cc2,cc11,cc2.icon_width_height,cc2.document_image,cc9[this.li]);
				}
				this.cc15=document.createElement("div");
				this.cc15.style.display="inline";
				if(ulm_ie)this.cc15.style.width=cc2.imgage_gap+cc8[0]+"px";
				else this.cc15.style.paddingLeft=cc2.imgage_gap+cc8[0]+"px";
				cc9[this.li].insertBefore(this.cc15,cc9[this.li].firstChild);
			}
		this.cc7++;
		}
	}
}

function create_images(cc2,cc11,iwh,iname,liobj){
	eval("tary=new Array("+iwh+")");
	this.cc15=document.createElement("div");
	this.cc15.style.display="inline";
	this.cc15.style.paddingLeft=cc2.imgage_gap+"px";
	liobj.insertBefore(this.cc15,liobj.firstChild);
	this.fi=document.createElement("IMG");
	this.fi.setAttribute("width",tary[0]);
	this.fi.setAttribute("height",tary[1]);
	this.fi.setAttribute("cc2_id",cc11);
	this.fi.className="plusminus";
	this.fi.src=iname;
	this.fi.style.verticalAlign="middle";
	this.fi.onclick=cc16;
	liobj.insertBefore(this.fi,liobj.firstChild);
}

function cc16(){
	cc18=this.getAttribute("cc2_id");
	cc17=this.parentNode.getElementsByTagName("UL");
	if(parseInt(this.parentNode.getAttribute("expanded"))){
		this.parentNode.setAttribute("expanded",0);
		cc17[0].style.display="none";
		this.parentNode.firstChild.src=cc3["img"+cc18].src;
	}else {
		this.parentNode.setAttribute("expanded",1);
		cc17[0].style.display="block";
		this.parentNode.firstChild.src=cc4["img"+cc18].src;
	}
}

function cc6(id,cc2){
	np_refix="#tmenu"+id;
	cc20="<style type='text/css'>";
	cc19="";if(ulm_ie)cc19="height:0px;font-size:1px;";
	cc20+=np_refix+" {width:100%;"+cc19+"-moz-user-select:none;margin:0px;padding:0px;list-style:none;"+cc2.main_container_styles+"}";
	cc20+=np_refix+" li{white-space:nowrap;list-style:none;margin:0px;padding:0px;"+cc2.main_item_styles+"}";
	cc20+=np_refix+" ul li{"+cc2.sub_item_styles+"}";
	cc20+=np_refix+" ul{list-style:none;margin:0px;padding:0px;padding-left:"+cc2.indent+"px;"+cc2.sub_container_styles+"}";
	cc20+=np_refix+" a{"+cc2.main_link_styles+"}";
	cc20+=np_refix+" a:hover{"+cc2.main_link_hover_styles+"}";
	cc20+=np_refix+" ul a{"+cc2.sub_link_styles+"}";
	cc20+=np_refix+" ul a:hover{"+cc2.sub_link_hover_styles+"}";
	cc20+=".ctmmainhover {"+cc2.main_expander_hover_styles+"}";
	if(cc2.sub_expander_hover_styles)cc20+=".ctmsubhover {"+cc2.sub_expander_hover_styles+"}";
	else cc20+=".ctmsubhover {"+cc2.main_expander_hover_styles+"}";
	if(cc2.use_hand_cursor)cc20+=np_refix+" li span,.plusminus{cursor:hand;cursor:pointer;}";
	else cc20+=np_refix+" li span,.plusminus{cursor:default;}";
	document.write(cc20+"</style>");
}

