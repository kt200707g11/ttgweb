function setCookie(e,t,o){if("undefined"==typeof o)document.cookie=e+"="+t+"; path=/";else{var s=new Date;s.setTime(s.getTime()+24*o*60*60*1e3);var n="expires="+s.toGMTString();document.cookie=e+"="+t+"; "+n+"; path=/"}}function getCookie(e){for(var t=e+"=",o=document.cookie.split(";"),s=0;s<o.length;s++){for(var n=o[s];" "==n.charAt(0);)n=n.substring(1);if(-1!=n.indexOf(t))return n.substring(t.length,n.length)}return""}function toogleScroll(){var e=$("#search-form").outerHeight(),t=$(".wrap-listing-item .inner-wrap").outerHeight();$(".wrap-listing").css("height",t-e+"px")}function getShowNumFrm(e){if(!e)return void $(".show-num-frm").each(function(){var e=$(this),t=0;e.find(".val-selected .selected").find("span").remove(),e.find(":input").not("input[type=hidden]").each(function(){""!=$(this).val()&&(t+=1)}),t>0&&e.find(".val-selected .selected").append('<span style="display: inline-block;padding-left:5px;">('+t+")</span>")});if(el=$(this),el.closest(".show-num-frm").length>0){var t=0;el.closest(".show-num-frm").find(":input").not("input[type=hidden]").each(function(){""!=$(this).val()&&(t+=1,el.closest(".show-num-frm").find(".val-selected .selected").find("span").remove(),el.closest(".show-num-frm").find(".val-selected .selected").append('<span style="display: inline-block;padding-left:5px;">('+t+")</span>")),0==t&&el.closest(".show-num-frm").find(".val-selected .selected").find("span").remove()})}}var desktop,form,events,$window=$(window),s={type:"#type"};$(document).ready(function(){events={mobileEvents:[],desktopEvents:[],attachMobileEvent:function(){events._attachEvent.apply(events.mobileEvents,arguments)},attachDesktopEvent:function(){events._attachEvent.apply(events.desktopEvents,arguments)},_attachEvent:function(){events.switchEvent("on",arguments),this.push(arguments)},detachMobileEvents:function(){events._detachEvents(events.mobileEvents),events._attachEvents(events.desktopEvents)},detachDesktopEvents:function(){events._detachEvents(events.desktopEvents),events._attachEvents(events.mobileEvents)},_detachEvents:function(e){for(var t in e)events.switchEvent("off",e[t])},_attachEvents:function(e){for(var t in e)events.switchEvent("on",e[t])},switchEvent:function(e,t){var o=[];for(var s in t)o.push(t[s]);var n=o.splice(0,1);n=n[0],n[e].apply(n,o)}},form={el:$("#search-form"),listSearchEl:$("#search-list"),mapSearchEl:$("#map-search"),autoFill:$("#map-search-wrap").find(".auto-fill"),fields:null,filterFields:function(e){form.fields.filter(function(){return!this.value}).prop("disabled",!0)},sortSubmit:function(){form.el.submit()},searchFocus:function(e){form.mapSearchEl.val(""),form.showSearchList(),form.listSearchEl.find(".hint-wrap").show();var t=getCookie("sh");if(t){form.listSearchEl.find(".center").show();for(var o=JSON.parse(t),s=o.length,n=0;s>n;n++)form.listSearchUl.append('<li><a data-history="1" class="search-item" href="javascript:;" data-id="'+o[n].i+'" data-type="'+o[n].t+'">'+o[n].v+"</a></li>")}else form.listSearchEl.find(".center").hide()},searchBlur:function(){form.mapSearchEl.val(form.mapSearchEl.data("val"))},searchTyping:function(){var e=form.mapSearchEl.val().trim(),t=form.fields.filter(s.type).val();e.length>1?($.data(this,"ajax")&&$.data(this,"ajax").abort(),$.data(this,"ajax",$.get("/api/v1/map/get",{v:e,t:t},function(e){if(form.listSearchUl.html(""),e.length){for(var t=0;t<e.length;t++){var o=e[t];form.listSearchUl.append('<li><a class="search-item" href="javascript:;" data-id="'+o.id+'" data-type="'+o.type+'">'+o.full_name+"</a></li>")}form.showSearchList(),form.listSearchEl.find(".hint-wrap").hide()}else form.hideSearchList_()}))):form.hideSearchList_()},searchItemClick:function(){var e=$(this),t=e.text(),o=e.data("type"),s=e.data("id");if(form.mapSearchEl.data("val",t).val(t),form.autoFill.val("").filter("#"+o+"_id").val(s),!e.data("history")){for(var n=getCookie("sh"),r=n?JSON.parse(n):[],a=!1,i=0;i<r.length;i++){var c=r[i];if(c.i==s&&c.t==o){a=!0;break}}a||(r.length>4&&r.pop(),r.unshift({v:t,i:s,t:o}),setCookie("sh",JSON.stringify(r)))}},searchListMouseEnter:function(){form.mapSearchEl.off("blur",form.searchBlur)},searchListMouseLeave:function(){form.mapSearchEl.on("blur",form.searchBlur)},showSearchList:function(){form.listSearchEl.hasClass("hide")&&(form.listSearchEl.removeClass("hide"),$(document).on("click",form.hideSearchList))},hideSearchList:function(e){var t=$(e.target);(t.hasClass("search-item")||0==t.closest("#map-search-wrap").length)&&form.hideSearchList_()},hideSearchList_:function(){form.listSearchEl.addClass("hide"),form.listSearchUl.html(""),$(document).off("click",form.hideSearchList)},preventEnterSubmit:function(e){return 13==e.keyCode?(e.preventDefault(),!1):void 0}},form.fields=form.el.find("select:not(.exclude), input:not(.exclude)"),form.fields.on("change",getShowNumFrm),events.attachMobileEvent(form.el,"submit",form.filterFields),events.attachMobileEvent(form.fields.filter("#order_by"),"change",form.sortSubmit),form.listSearchUl=form.listSearchEl.find("ul"),form.mapSearchEl.on("focus",form.searchFocus).on("keyup",form.searchTyping).on("blur",form.searchBlur).on("keydown",form.preventEnterSubmit),form.listSearchEl.on("click","a",form.searchItemClick),form.listSearchEl.on("mouseenter",form.searchListMouseEnter).on("mouseleave",form.searchListMouseLeave),desktop={isLoadedResources:!1,countLoadedResource:0,checkToEnable:function(){desktop.isDesktop()&&!desktop.isEnabled?desktop.enable():!desktop.isDesktop()&&desktop.isEnabled&&desktop.disable()},isDesktop:function(){return"none"==$(".m-header").css("display")},enable:function(){desktop.isEnabled=!0,events.detachMobileEvents(),desktop.isLoadedResources||(desktop.isLoadedResources=!0,desktop.loadResources())},disable:function(){desktop.isEnabled=!1,events.detachDesktopEvents()},loadResources:function(){var e=document.getElementsByTagName("head")[0];for(var t in resources){var o=document.createElement("script");o.src=resources[t],e.appendChild(o)}},loadedResource:function(){desktop.countLoadedResource++,desktop.countLoadedResource==resources.length&&m2Map.initMap()}},desktop.checkToEnable(),$window.on("resize",desktop.checkToEnable),toogleScroll(),getShowNumFrm(),$window.on("resize",toogleScroll),$(".advande-search").toggleShowMobi({btnEvent:".btn-submit",itemToggle:".toggle-search"}),$(".dropdown-common").dropdown({txtAdd:!0,styleShow:0}),$("#type").on("change",function(){$(".select-price .val-selected div span").hide(),$(".select-price .box-dropdown").price_dt({rebuild:!0}),1==$(this).val()?$(".select-price .box-dropdown").price_dt({hinhthuc:"mua"}):2==$(this).val()?$(".select-price .box-dropdown").price_dt({hinhthuc:"thue"}):$(".select-price .box-dropdown").price_dt()});var e=$(".select-others #type").val();1==e?$(".select-price .box-dropdown").price_dt({hinhthuc:"mua"}):2==e?$(".select-price .box-dropdown").price_dt({hinhthuc:"thue"}):$(".select-price .box-dropdown").price_dt(),$(".select-dt .box-dropdown").price_dt()});