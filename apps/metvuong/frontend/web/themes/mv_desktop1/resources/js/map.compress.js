/*
 * Author: qtlenh@gmail.com
 * Date: 10/06/2016
 */
function ci(e){var t=document.createElement("img");return t.src=e,t}function measureText(e,t){var a=document.createElement("span");a.style.font=t,a.style.visibility="hidden",a.style.position="fixed",a.style.zIndex=-1,a.appendChild(document.createTextNode(e)),document.body.appendChild(a);var o=a.getBoundingClientRect().width;return document.body.removeChild(a),o}function createMarkerIcon(e,t,a){var o=document.createElement("canvas"),r=o.getContext("2d");return o.width=e.width,o.height=e.height,r.drawImage(e,0,0,e.width,e.height),r.font=a.font,r.fillStyle=a.color,r.fillText(t,a.left,a.top),o.toDataURL()}function decodeGeometry(e){e=JSON.parse(e);for(var t=e.length,a=[],o=0;t>o;o++)a.push(google.maps.geometry.encoding.decodePath(e[o]));return a}function Area(e,t){this.attrs=e,this.type=t}function Product(e){this.attrs=e}function initInfoBox(){ib=function(e){google.maps.OverlayView.call(this),"undefined"==typeof e&&(e={}),this.opts=e,this.opts.position||(this.opts.position="top"),this.opts.offsetLeft||(this.opts.offsetLeft=0),this.opts.panPadding||(this.opts.panPadding={top:0,right:0,bottom:0,left:0}),this.viewHolder=document.createElement("div"),this.viewHolder.style.position="absolute",this.viewHolder.className=this.opts.position,this.opts.content&&this.setContent(this.opts.content)},ib.prototype=new google.maps.OverlayView,ib.prototype.setPosition=function(e){this.opts.position=e,this.viewHolder.className=this.opts.position},ib.prototype.draw=function(){var e=this.getProjection().fromLatLngToDivPixel(this.position);this.viewHolder.style.left=e.x-this.viewHolder.offsetWidth/2+this.opts.offsetLeft+"px";var t=e.y-this.viewHolder.offsetHeight;"top"==this.opts.position?this.anchor instanceof google.maps.Marker&&(t-=this.anchor.getShape().coords[3]):t+=this.viewHolder.offsetHeight,this.viewHolder.style.top=t+"px",this.opts.disableAutoPan||this.boundsChangedListener&&this.panMap(),this.opts.onDraw&&this.opts.onDraw.apply(this)},ib.prototype.remove=function(){this.viewHolder.parentNode.removeChild(this.viewHolder)},ib.prototype.onAdd=function(){var e=this.getPanes();e.floatPane.appendChild(this.viewHolder);var t=this;this.opts.disableAutoPan||(this.boundsChangedListener=google.maps.event.addListener(this.map,"bounds_changed",function(){return t.panMap.apply(t)}))},ib.prototype.open=function(e,t){var a,o;e instanceof google.maps.Marker?(a=e.getPosition(),o=e.getMap()):e instanceof google.maps.Map&&(a=t?t:e.getCenter(),o=e),(this.anchor!==e||this.position.lat()!=a.lat()&&this.position.lng()!=a.lng())&&(this.anchor=e,this.position=a,this.setMap(o))},ib.prototype.close=function(){this.anchor=null,this.position=null,this.setMap(null)},ib.prototype.setContent=function(e){if(this.content=e,"string"==typeof e)this.viewHolder.innerHTML=e;else{for(;this.viewHolder.firstChild;)this.viewHolder.removeChild(this.viewHolder.firstChild);this.viewHolder.appendChild(e)}},ib.prototype.panMap=function(){var e=this.getMap(),t=e.getBounds();if(t){var a=t.getSouthWest().lng(),o=t.getNorthEast().lng(),r=t.getNorthEast().lat(),i=t.getSouthWest().lat(),n=this.getBounds(),s=n.getSouthWest().lng(),l=n.getNorthEast().lng(),p=n.getNorthEast().lat(),m=n.getSouthWest().lat(),d=(a>s?a-s:0)+(l>o?o-l:0),f=(p>r?r-p:0)+(i>m?i-m:0),c=e.getCenter(),g=c.lng()-d,u=c.lat()-f;e.panTo(new google.maps.LatLng(u,g)),google.maps.event.removeListener(this.boundsChangedListener),this.boundsChangedListener=null}},ib.prototype.getBounds=function(){var e=this.getMap();if(e){var t=e.getBounds();if(t){var a=e.getDiv(),o=a.offsetWidth,r=a.offsetHeight,i=t.toSpan(),n=i.lng(),s=i.lat(),l=n/o,p=s/r;if("top"==this.opts.position)var m=this.anchor instanceof google.maps.Marker?this.anchor.getShape().coords[3]:0;else var m=this.anchor instanceof google.maps.Marker?-this.viewHolder.offsetHeight:0;var d=this.position.lng()+(-(this.viewHolder.offsetWidth/2-this.opts.offsetLeft)-this.opts.panPadding.left)*l,f=this.position.lng()+(this.viewHolder.offsetWidth/2+this.opts.offsetLeft+this.opts.panPadding.right)*l,c=this.position.lat()-(-this.viewHolder.offsetHeight-m-this.opts.panPadding.top)*p,g=this.position.lat()-(this.opts.panPadding.bottom-m)*p;return new google.maps.LatLngBounds(new google.maps.LatLng(g,d),new google.maps.LatLng(c,f))}}}}var mi={PADDING:8,MIN_WIDTH:24,IMG:null,IMG_HOVER:null,COLOR:"black",COLOR_HOVER:"white",FONT:"12px Arial",ARROW_HEIGHT:0,TEXT_HEIGHT:0,MAX_WIDTH:0,MAX_HEIGHT:0,create:function(e,t){var a=t?mi.IMG_HOVER:mi.IMG,o=t?mi.COLOR_HOVER:mi.COLOR,r={color:o,font:mi.FONT},i=Math.round(measureText(e,r.font)),n=i+2*mi.PADDING;n<=mi.MIN_WIDTH?(n=mi.MIN_WIDTH,r.left=(n-i)/2):r.left=mi.PADDING;var s=Math.round(n*a.height/a.width),l=s*mi.ARROW_HEIGHT;return r.top=(s-l)/2+mi.TEXT_HEIGHT,a.width=n,a.height=s,mi.MAX_WIDTH<n&&(mi.MAX_WIDTH=n,mi.MAX_HEIGHT=s),{icon:createMarkerIcon(a,e,r),width:n,height:s}}};mi.IMG=ci("data:image/png;base64,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"),mi.IMG_HOVER=ci("data:image/png;base64,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"),mi.ARROW_HEIGHT=4/24,mi.TEXT_HEIGHT=4.5,Area.prototype.getPaths=function(){var e,t=this.attrs;return t.geometry&&(e=decodeGeometry(t.geometry)),e},Area.prototype.getBounds=function(){var e,t=this.getPaths();if(t){e=new google.maps.LatLngBounds;for(var a=t.length,o=0;a>o;o++)for(var r=t[o],i=r.length,n=0;i>n;n++)e.extend(r[n])}return e},Area.prototype.getCenter=function(){var e,t=this.attrs;if(t.center)centerP=JSON.parse(t.center),e=new google.maps.LatLng(centerP[0],centerP[1]);else{var a=this.getBounds();a&&(e=a.getCenter())}return e},Area.prototype.getName=function(){var e=this.attrs;return e.pre?e.pre+" "+e.name:e.name},Area.prototype.draw=function(e){var t=this.getPaths(),a=this;if("street"==this.type)t&&(this.poly=new google.maps.Polyline({map:e,path:t[0],strokeColor:m2Map.polygonColor,strokeOpacity:.8,strokeWeight:6,geodesic:!0}));else{t&&(this.poly=new google.maps.Polygon({map:e,paths:t,strokeColor:m2Map.polygonColor,strokeOpacity:.8,strokeWeight:1,fillColor:m2Map.polygonColor,fillOpacity:.2}),this.poly.addListener("mouseover",function(){a.marker&&a.marker.set("clickable",!1),a.mouseover()}),this.poly.addListener("mouseout",function(){a.marker&&a.marker.set("clickable",!0),a.mouseout()}),this.poly.addListener("click",function(){a.nextZoomLevel()}));var o=this.getCenter();o&&this.attrs.total>0&&(this.marker=new google.maps.Marker({map:e,position:o}),m2Map.setIcon(this.marker,this.attrs.total,0),this.marker.addListener("mouseover",function(){a.mouseover()}),this.marker.addListener("mouseout",function(){a.mouseout()}),this.marker.addListener("click",function(){a.nextZoomLevel()}))}},Area.prototype.remove=function(){this.poly&&this.poly.setMap(null),this.marker&&this.marker.setMap(null)},Area.prototype.mouseover=function(){m2Map.infoBoxHover.setContent('<div class="info-wrap-single"><div style="padding: 6px 12px; font-weight: bold; font-size: 13px; white-space: nowrap">'+this.getName()+'</div><div class="info-arrow"></div></div>'),this.marker?m2Map.infoBoxHover.open(this.marker):m2Map.infoBoxHover.open(m2Map.map,this.getCenter())},Area.prototype.mouseout=function(){m2Map.infoBoxHover.close()},Area.prototype.nextZoomLevel=function(){for(var e=m2Map.map.getZoom(),t=e+1,a=form.af.filter(s.iz).val(),o=form.getFocusLocation(),r=m2Map.getZoomAreaLevel(e,a,o.type),i=o.type;m2Map.getZoomAreaLevel(t,a,i)==r&&t<m2Map.detailZoomLevel;)t++;m2Map.map.setCenter(this.getCenter()),m2Map.map.setZoom(t)},Product.prototype.getMarkerKey=function(){return this.attrs.lat+"-"+this.attrs.lng},Product.prototype.getPosition=function(){return new google.maps.LatLng(this.attrs.lat,this.attrs.lng)},Product.prototype.getImage=function(){return this.attrs.f?this.attrs.d?"/store/"+this.attrs.d+"/240x180/"+this.attrs.f:this.attrs.f:"/themes/metvuong2/resources/images/default-ads.jpg"},Product.prototype.getPrice=function(){return formatPrice(this.attrs.price)+" "+lajax.t("VND")},Product.prototype.getAddress=function(){return this.attrs.a},Product.prototype.getAdditionInfo=function(){var e=[];return e.push('<span class="icon-mv"><span class="icon-page-1-copy"></span></span>'+this.attrs.area+"m<sup>2</sup>"),this.attrs.room_no&&"0"!=this.attrs.room_no&&e.push('<span class="icon-mv"><span class="icon-bed-search"></span></span>'+this.attrs.room_no),this.attrs.toilet_no&&"0"!=this.attrs.toilet_no&&e.push('<span class="icon-mv"><span class="icon-icon-bathroom"></span></span>'+this.attrs.toilet_no),e.join('<i class="s"></i>')},s.rect="#rect",s.ra="#ra",s.rm="#rm",s.rl="#rl",s.raK="#ra_k",s.iz="#iz",s.z="#z",s.c="#c",s.page="#page",s.did="#did";var contentHolder=$("#content-holder"),detailListingWrap=$(".detail-listing-dt"),m2Map={infoDetailWidth:174,infoDetailHeight:98,loadingTimeout:null,loadingList:$("#loading-list"),progressBar:$("#progress-bar"),mapEl:$("#map"),polygonColor:"#00a769",map:null,markers:{},areas:{},areasLevel:{city:3,district:2,ward:1},deteilZoomLevelDefault:16,detailZoomLevel:16,infoBoxHover:null,boundsChangedEvent:null,zoomChangedEvent:null,closeDetailListener:null,currentDrawState:null,markerIconCached:{},shape:{coords:[0,0,24,28],type:"rect"},wrapListing:$(".wrap-listing"),initMap:function(){History.Adapter.bind(window,"statechange",m2Map.stateChange),initInfoBox(),m2Map.infoBoxHover=new ib({disableAutoPan:!0,position:"top"}),m2Map.infoBoxDetailHover=new ib({disableAutoPan:!0,position:"top"});var e=form.afZoom.val(),t=form.afCenter.val();m2Map.mapOptions={center:{lat:10.783091,lng:106.704899},zoom:18,mapTypeControl:!0,mapTypeControlOptions:{style:google.maps.MapTypeControlStyle.DROPDOWN_MENU}},e&&t?(m2Map.initMapRe(e,t),m2Map.hasMapInstance()):m2Map.initMapFresh(m2Map.hasMapInstance)},hasMapInstance:function(){var e=form.af.filter(s.did).val();e&&m2Map.detail(e),m2Map.addDrawControl()},stateChange:function(){},pushState:function(e){e||(e=form.serialize()),e=decodeURIComponent(e),History.pushState({},document.title,actionId+"?"+e)},initMapRe:function(e,t){m2Map.mapOptions.center=m2Map.urlValueToLatLng(t),m2Map.mapOptions.zoom=Number(e);m2Map.map=new google.maps.Map(m2Map.mapEl.get(0),m2Map.mapOptions);m2Map.initMapReFirstLoad(),google.maps.event.addListenerOnce(m2Map.map,"idle",m2Map.InitMapReIdle),m2Map.detectHasChange()},initMapReFirstLoad:function(){var e=form.getFocusLocation();if("project_building"==e.type)m2Map.currentDrawState="project_building",m2Map.changeLocation(m2Map.drawBuildingProject);else if("street"==e.type)m2Map.currentDrawState=e.type,form.af.filter(s.rl).val(""),form.af.filter(s.ra).val(e.type),form.af.filter(s.raK).val("id"),m2Map.ajaxRequest=m2Map.get(function(e){m2Map.drawStreet(e),e.ra&&e.ra.length&&m2Map.drawArea(new Area(e.ra[0],"street"))});else{var t=Number(form.af.filter(s.iz).val()),a=m2Map.mapOptions.zoom,o={ward:0,district:1,city:2};if(t+o[e.type]>=m2Map.deteilZoomLevelDefault?"city"==e.type?m2Map.detailZoomLevel=t+3:"district"==e.type?m2Map.detailZoomLevel=t+2:"ward"==e.type&&(m2Map.detailZoomLevel=t+1):m2Map.detailZoomLevel=m2Map.deteilZoomLevelDefault,a<m2Map.detailZoomLevel){var r=m2Map.getZoomAreaLevel(a,t,e.type);m2Map.currentDrawState=r,form.af.filter(s.ra).val(r);var i=e.type==r?"id":e.type+"_id";form.af.filter(s.raK).val(i);var n=form.fields.filter(s.rect).prop("disabled",!0);m2Map.ajaxRequest=m2Map.get(function(e){e.ra&&m2Map.drawAreas(e.ra,r)}),n.prop("disabled",!1)}else m2Map.currentDrawState="detail",m2Map.loadDetail("")}},InitMapReIdle:function(){form.afRect.val(m2Map.getBounds(28,12,0,12).toUrlValue()),m2Map.boundsChangedEvent=m2Map.map.addListener("bounds_changed",m2Map.boundsChanged),m2Map.zoomChangedEvent=m2Map.map.addListener("zoom_changed",m2Map.zoomChanged)},initMapFresh:function(e){m2Map.changeLocation(function(t){m2Map.drawMap(t),e()})},drawMap:function(e){m2Map.map=new google.maps.Map(m2Map.mapEl.get(0),m2Map.mapOptions);m2Map.drawLocation(e),m2Map.detectHasChange(),google.maps.event.addListenerOnce(m2Map.map,"bounds_changed",m2Map.setInitLocationProps)},detectHasChange:function(){var e,t,a=m2Map.map;google.maps.event.addListenerOnce(m2Map.map,"bounds_changed",function(){var o=a.getZoom();e=o,t=a.getCenter().toString()}),google.maps.event.addListenerOnce(m2Map.map,"idle",function(){var o=a.getZoom();if(o!=e||a.getCenter().toString()!=t){var r=form.getFocusLocation();"project_building"==r.type?"project_building"==m2Map.currentDrawState:"street"==r.type?"street"==m2Map.currentDrawState:o<m2Map.detailZoomLevel?(m2Map.zoomChanged(),m2Map.removeAllDetail()):(m2Map.currentDrawState="detail",m2Map.removeAreas(),m2Map.ajaxRequest&&(m2Map.ajaxRequest.abort(),m2Map.ajaxRequest=null),m2Map.infoBoxHover.close(),form.af.filter(s.page).val(""),form.af.filter(s.ra).val(""),form.af.filter(s.rm).val(1),form.af.filter(s.rl).val(1),m2Map.ajaxRequest=m2Map.get(function(e){m2Map.removeAreas(),m2Map.drawDetailCallBack(e)})),form.afRect.val(m2Map.getBounds(28,12,0,12).toUrlValue()),form.afZoom.val(m2Map.map.getZoom()),form.afCenter.val(m2Map.getCenter().toUrlValue()),m2Map.pushState()}})},setInitLocationProps:function(){var e=m2Map.map.getZoom(),t=form.getFocusLocation();m2Map.currentDrawState=t.type,form.af.filter(s.iz).val(e);var a={ward:0,district:1,city:2};e+a[t.type]>=m2Map.deteilZoomLevelDefault?"city"==t.type?m2Map.detailZoomLevel=e+3:"district"==t.type?m2Map.detailZoomLevel=e+2:"ward"==t.type&&(m2Map.detailZoomLevel=e+1):m2Map.detailZoomLevel=m2Map.deteilZoomLevelDefault},boundsChanged:function(){var e=m2Map.map;clearTimeout(e.get("bounds_changed_timeout")),e.set("bounds_changed_timeout",setTimeout(function(){form.afRect.val(m2Map.getBounds(28,12,0,12).toUrlValue()),form.afZoom.val(m2Map.map.getZoom()),form.afCenter.val(m2Map.getCenter().toUrlValue()),"detail"==m2Map.currentDrawState?m2Map.loadDetail(1):"street"==m2Map.currentDrawState&&m2Map.loadDetail(1),m2Map.pushState()},100))},zoomChanged:function(){var e=m2Map.map.getZoom();if("project_building"==m2Map.currentDrawState||"street"==m2Map.currentDrawState)return!1;if(e<m2Map.detailZoomLevel){form.af.filter(s.rl).val(""),"detail"==m2Map.currentDrawState&&(m2Map.infoBoxDetailHover.close(),m2Map.removeAllDetail(),form.af.filter(s.rm).val(""),form.af.filter(s.page).val(""),form.af.filter(s.rl).val(1));var t=form.getFocusLocation(),a=m2Map.getZoomAreaLevel(m2Map.map.getZoom(),form.af.filter(s.iz).val(),t.type);if(m2Map.currentDrawState!=a){m2Map.infoBoxHover.close(),m2Map.ajaxRequest&&(m2Map.ajaxRequest.abort(),m2Map.ajaxRequest=null),m2Map.infoBoxHover.close(),m2Map.currentDrawState=a,form.af.filter(s.ra).val(a);var o=t.type==a?"id":t.type+"_id";form.af.filter(s.raK).val(o);var r=form.fields.filter(s.rect).prop("disabled",!0);m2Map.ajaxRequest=m2Map.get(function(e){e.ra&&m2Map.drawAreas(e.ra,a),e.rl&&m2Map.drawList(e.rl)}),r.prop("disabled",!1)}}else m2Map.infoBoxHover.close(),m2Map.currentDrawState="detail",m2Map.removeAreas()},loadDetail:function(e){m2Map.ajaxRequest&&(m2Map.ajaxRequest.abort(),m2Map.ajaxRequest=null),m2Map.infoBoxHover.close(),form.af.filter(s.page).val(""),form.af.filter(s.ra).val(""),form.af.filter(s.rm).val(1),form.af.filter(s.rl).val(e),m2Map.ajaxRequest=m2Map.get(m2Map.drawDetailCallBack)},drawLocation:function(e){var t=form.getFocusLocation();if(e.rm&&("street"==t.type?m2Map.drawStreet(e):m2Map.drawBuildingProject(e,!0)),e.ra&&e.ra.length){var a=new Area(e.ra[0],t.type);if(m2Map.drawAndFitArea(a),"street"==t.type){var o=a.getBounds(),r=a.getCenter();o||r||e.rm&&e.rm.length&&m2Map.setCenter({lat:Number(e.rm[0].lat),lng:Number(e.rm[0].lng)})}}google.maps.event.addListenerOnce(m2Map.map,"idle",m2Map.drawLocationCallback)},drawBuildingProject:function(e,t){m2Map.removeAllDetail(),m2Map.removeAreas();var a=$(".infor-duan-suggest");if(a.length&&a.data("lat")&&a.data("lng"))var o={lat:a.data("lat"),lng:a.data("lng")};else if(e.rm.length)var o={lat:Number(e.rm[0].lat),lng:Number(e.rm[0].lng)};if(o){t===!0&&(m2Map.setCenter(o),m2Map.map.setZoom(m2Map.deteilZoomLevelDefault));var r=new google.maps.Marker({map:m2Map.map,position:o}),i=[];for(var n in e.rm)i.push(new Product(e.rm[n]));r.addListener("click",m2Map.markerClick),r.set("products",i),m2Map.setIcon(r,i.length,0),m2Map.markers[o]=r}},drawLocationCallback:function(){null==m2Map.boundsChangedEvent&&(m2Map.boundsChangedEvent=m2Map.map.addListener("bounds_changed",m2Map.boundsChanged)),null==m2Map.zoomChangedEvent&&(m2Map.zoomChangedEvent=m2Map.map.addListener("zoom_changed",m2Map.zoomChanged))},drawAndFitArea:function(e){var t=e.getBounds();if(t)m2Map.fitBounds(e.getBounds());else{var a=e.getCenter();a&&m2Map.setCenter(a)}m2Map.drawArea(e)},changeLocation:function(e){var t=form.getFocusLocation();"project_building"==t.type?form.af.filter(s.rm).val(1):(form.af.filter(s.ra).val(t.type),form.af.filter(s.raK).val("id"),"street"==t.type&&form.af.filter(s.rm).val(1));var a=form.fields.filter(s.rect);"project_building"==t.type&&a.prop("disabled",!0),m2Map.get(e),"project_building"==t.type&&a.prop("disabled",!1)},drawStreet:function(e){e.rm&&(m2Map.removeAllDetail(),m2Map.drawDetail(e.rm))},removeAreas:function(){var e=m2Map.areas;e.length;for(var t in e)e[t].remove();m2Map.areas={}},drawAreas:function(e,t){m2Map.removeAreas();for(var a=e.length,o=0;a>o;o++){var r=new Area(e[o],t);m2Map.drawArea(r)}},drawArea:function(e){e.draw(m2Map.map),m2Map.areas[e.attrs.id]=e},drawDetailCallBack:function(e){e.rm&&(m2Map.removeAllDetail(),m2Map.drawDetail(e.rm)),e.rl&&m2Map.drawList(e.rl)},drawDetail:function(e){for(var t=m2Map.map,a=m2Map.markers,o=e.length,r=0;o>r;r++){var i=new Product(e[r]),n=i.getMarkerKey(),s=a[n];if(s){var l=s.get("products");l.push(i),s.set("products",l),m2Map.setIcon(s,l.length,0)}else s=new google.maps.Marker({map:t,position:i.getPosition()}),s.addListener("click",m2Map.markerClick),s.addListener("mouseover",m2Map.markerMouseOver),s.addListener("mouseout",m2Map.markerMouseOut),s.set("products",[i]),m2Map.setIcon(s,1,0),a[n]=s}},markerMouseOver:function(){clearTimeout(m2Map.markerMouseOutT);var e=this.get("products"),t=e[0];m2Map.setIcon(this,e.length,1),this.setZIndex(google.maps.Marker.MAX_ZINDEX++);var a=t.getImage(),o=t.getPrice(),r=t.getAddress(),i=t.getAdditionInfo(),n=m2Map.getMarkerPadding(this);if(n.left<m2Map.infoDetailWidth)var s=n.left<m2Map.infoDetailWidth?m2Map.infoDetailWidth-n.left+6:0;else var s=n.right<m2Map.infoDetailWidth?n.right-m2Map.infoDetailWidth-6:0;n.top-this.getShape().coords[3]<m2Map.infoDetailHeight+11?m2Map.infoBoxDetailHover.setPosition("bottom"):m2Map.infoBoxDetailHover.setPosition("top"),m2Map.infoBoxDetailHover.opts.offsetLeft=s;var l='<div class="info-wrap-detail info-wrap-single"><div class="clearfix"><div class="img-show left"><div><img src="'+a+'"></div></div><div class="right"><div class="rest-inside"><div class="address">'+r+'</div><div class="price">'+o+'</div><div class="addition">'+i+'</div></div></div></div><div class="info-arrow" style="margin-left: '+-Math.round(s+5)+'px"></div></div>';m2Map.infoBoxDetailHover.setContent(l),m2Map.infoBoxDetailHover.open(this)},markerMouseOut:function(){m2Map.markerMouseOutT=setTimeout(function(){m2Map.infoBoxDetailHover.close()},150);var e=this.get("products");m2Map.setIcon(this,e.length,0)},markerClick:function(){var e=this.get("products");if(1==e.length){var t=e[0];m2Map.showDetail(t.attrs.id)}},removeAllDetail:function(){var e=m2Map.markers;for(var t in e)e[t].setMap(null);m2Map.markers={}},drawList:function(e){contentHolder.html(e)},getBounds:function(e,t,a,o){var r=m2Map.map.getBounds(),i=r.getSouthWest(),n=r.getNorthEast(),s=m2Map.getScaleOffset(r),l=i.lat()+a*s.y,p=i.lng()+o*s.x,m=n.lat()-e*s.y,d=n.lng()-t*s.x;return new google.maps.LatLngBounds(new google.maps.LatLng(l,p),new google.maps.LatLng(m,d))},getScaleOffset:function(e){var t=m2Map.map.getDiv(),a=t.offsetWidth,o=t.offsetHeight,r=e.toSpan(),i=r.lat(),n=r.lng(),s=n/a,l=i/o;return{x:s,y:l}},getMarkerPadding:function(e){var t=m2Map.map.getBounds(),a=m2Map.getScaleOffset(t),o={top:(t.getNorthEast().lat()-e.getPosition().lat())/a.y,bottom:(e.getPosition().lat()-t.getSouthWest().lat())/a.y,left:(e.getPosition().lng()-t.getSouthWest().lng())/a.x,right:(t.getNorthEast().lng()-e.getPosition().lng())/a.x};return o},getCenter:function(){return m2Map.map.getCenter()},setCenter:function(e){m2Map.map.setCenter(e)},fitBounds:function(e){m2Map.map.fitBounds(e)},setIcon:function(e,t,a){if(1==t){var o="/images/marker-"+a+".png";e.setShape(m2Map.shape)}else{var r=t+"-"+a,i=m2Map.markerIconCached[r];if(i)var n=i;else{var n=mi.create(t,a);m2Map.markerIconCached[r]=n}var o=n.icon,s={coords:[0,0,n.width,n.height],type:"rect"};e.setShape(s)}e.setIcon(o)},get:function(e,t){t||(t=form.serialize());var a=form.af.filter(s.rl).val(),o=form.af.filter(s.rm),r=form.af.filter(s.ra).val()||o.val()&&!o.prop("disabled");return r&&m2Map.loading(10),a&&m2Map.loadingList.show(),$.ajax({url:"/map/get",data:t,success:e,complete:function(){a&&(m2Map.loadingList.hide(),m2Map.wrapListing.scrollTop(0)),r&&m2Map.loaded()}})},urlValueToLatLng:function(e){var t=e.split(",");return new google.maps.LatLng(t[0],t[1])},getZoomAreaLevel:function(e,t,a){t=Number(t);var o=m2Map.areasLevel[a],r=m2Map.detailZoomLevel-t,i=m2Map.detailZoomLevel-e;if(i>r)return a;var n=Math.ceil(r/o),s=Math.ceil((r-n)/(o-1))+n;return n>=i?"ward":s>=i?"district":"city"},getFocusMarker:function(e){var t=m2Map.markers;for(var a in t)for(var o=t[a],r=o.get("products"),i=r.length,n=0;i>n;n++)if(r[n].attrs.id==e)return o},focusMarker:function(e){m2Map.setIcon(e,e.get("products").length,1),e.setZIndex(google.maps.Marker.MAX_ZINDEX++)},showDetail:function(e){m2Map.detail(e),form.af.filter(s.did).val(e),m2Map.pushState()},detail:function(e){var t=$(".wrap-listing-item .inner-wrap").outerWidth(),a=$(".detail-listing");detailListingWrap.loading({full:!1}),detailListingWrap.css({right:t+"px"}),google.maps.event.removeListener(m2Map.closeDetailListener),m2Map.closeDetailListener=m2Map.map.addListener("click",m2Map.closeDetail),$.get("/listing/detail",{id:e},function(e){var t=$(e).find("#detail-wrap");t.find(".popup-common").each(function(){var e=$(this),t=e.attr("id");$("body").find("#"+t).remove()}),a.find(".container").html($(e).find("#detail-wrap").html()),a.find(".popup-common").appendTo("body");new Swiper(".swiper-container",{pagination:".swiper-pagination",paginationClickable:!0,spaceBetween:0});detailListingWrap.loading({done:!0}),$(".inner-detail-listing").scrollTop(0),$(".btn-extra").attr("href",a.find(".btn-copy").data("clipboard-text"))})},closeDetail:function(e){e.preventDefault&&e.preventDefault();var t=$(".wrap-listing-item .inner-wrap").outerWidth();detailListingWrap.css({right:-t+"px"}),form.af.filter(s.did).val(""),m2Map.pushState()},loading:function(e){m2Map.progressBar.show(),m2Map.loading_(e)},loading_:function(e){m2Map.progressBar.width(e+"%"),90>e&&(m2Map.loadingTimeout=setTimeout(function(){m2Map.loading_(e+10)},10*e))},loaded:function(){clearTimeout(m2Map.loadingTimeout),m2Map.progressBar.width("100%"),m2Map.loadingTimeout=setTimeout(function(){m2Map.progressBar.hide().width("0%")},150)},addDrawControl:function(){var e=document.createElement("div");e.className="draw-wrap",e.index=1;var t=document.createElement("a");t.className="button draw-button",t.innerHTML='<span class="icon-mv"><span class="icon-edit-copy-4"></span></span>Vẽ khoanh vùng';var a=document.createElement("a");a.className="button remove-button",a.innerHTML='<span class="icon-mv"><span class="icon-close-icon"></span></span>Xóa khoanh vùng',e.appendChild(t),e.appendChild(a),m2Map.map.controls[google.maps.ControlPosition.TOP_LEFT].push(e)}};form.af=$("#af-wrap").children(),form.afRect=form.af.filter(s.rect),form.afZoom=form.af.filter(s.z),form.afCenter=form.af.filter(s.c),form.projectInfoEl=$("#project-info"),form.formChange=function(e){var t=$(e.target);if(form.af.filter(s.rl).val(1),t.hasClass("search-item")){if(form.af.val(""),form.af.filter(s.rl).val(1),google.maps.event.removeListener(m2Map.boundsChangedEvent),m2Map.boundsChangedEvent=null,google.maps.event.removeListener(m2Map.zoomChangedEvent),m2Map.zoomChangedEvent=null,google.maps.event.addListenerOnce(m2Map.map,"bounds_changed",m2Map.setInitLocationProps),m2Map.removeAllDetail(),"project_building"==t.data("type")){form.projectInfoEl.html("");var a=t.data("id");$.get(loadProjectUrl,{id:a},function(e){form.projectInfoEl.html(e),toogleScroll()})}else form.projectInfoEl.html(""),toogleScroll();m2Map.changeLocation(function(e){m2Map.removeAreas(),m2Map.drawLocation(e),e.rl&&m2Map.drawList(e.rl)})}else if("order_by"==t.attr("id")){form.af.filter(s.ra).val(""),form.af.filter(s.raK).val(""),form.af.filter(s.page).val(""),m2Map.pushState();var o=form.fields.filter(s.rect);"city"==m2Map.currentDrawState||"district"==m2Map.currentDrawState||"ward"==m2Map.currentDrawState||"project_building"==m2Map.currentDrawState?(o.prop("disabled",!0),form.af.filter(s.rm).prop("disabled",!0)):form.af.filter(s.rm).val(1),form.af.filter(s.rl).val(1),m2Map.get(m2Map.drawDetailCallBack),o.prop("disabled",!1),form.af.filter(s.rm).prop("disabled",!1)}else{form.af.filter(s.rl).val(1),form.af.filter(s.page).val("");var o=form.fields.filter(s.rect);if("city"==m2Map.currentDrawState||"district"==m2Map.currentDrawState||"ward"==m2Map.currentDrawState){o.prop("disabled",!0),form.af.filter(s.rm).val("");var r=form.getFocusLocation(),i=m2Map.getZoomAreaLevel(m2Map.map.getZoom(),form.af.filter(s.iz).val(),r.type);form.af.filter(s.ra).val(i);var n=r.type==i?"id":r.type+"_id";form.af.filter(s.raK).val(n)}else form.af.filter(s.rm).val(1),form.af.filter(s.ra).val("");m2Map.get(function(e){m2Map.drawList(e.rl),e.rm&&(m2Map.removeAllDetail(),"project_building"==m2Map.currentDrawState?m2Map.drawBuildingProject(e):m2Map.drawDetail(e.rm)),e.ra&&m2Map.drawAreas(e.ra,i)}),o.prop("disabled",!1)}m2Map.pushState()},form.serialize=function(){return form.serialize_(form.fields)},form.serialize_=function(e){return e.filter(function(){return!!this.value}).serialize()},form.toggleConditionFields=function(){desktop.isDesktop()?form.af.prop("disabled",!1):form.af.prop("disabled",!0)},form.getFocusLocation=function(){var e={};return form.autoFill.each(function(){var t=$(this),a=t.val();a&&(e.id=a,e.type=t.attr("id").replace("_id",""))}),e},form.pagination=function(e){e.preventDefault(),form.af.filter(s.ra).val(""),form.af.filter(s.raK).val("");var t=Number($(this).data("page"))+1,a=form.af.filter(s.page);1==t?a.val(""):a.val(t),m2Map.pushState();var o=form.fields.filter(s.rect);("city"==m2Map.currentDrawState||"district"==m2Map.currentDrawState||"ward"==m2Map.currentDrawState)&&o.prop("disabled",!0),form.af.filter(s.rl).val(1),form.af.filter(s.rm).prop("disabled",!0),m2Map.get(form.paginationCallback),form.af.filter(s.rl).val(""),form.af.filter(s.rm).prop("disabled",!1),o.prop("disabled",!1)},form.paginationCallback=function(e){m2Map.drawList(e.rl)},form.itemClick=function(e){e.preventDefault();var t=$(this).data("id");m2Map.showDetail(t)},form.itemMouseEnter=function(e){var t=$(this).data("id");$.data(this,"mouseenterTimer",setTimeout(function(){var e=m2Map.getFocusMarker(t);e&&m2Map.focusMarker(e)},300))},form.itemMouseLeave=function(e){clearTimeout($.data(this,"mouseenterTimer"));var t=m2Map.getFocusMarker($(this).data("id"));t&&m2Map.setIcon(t,t.get("products").length,0)},events.attachDesktopEvent(form.fields,"change",form.formChange),events.attachDesktopEvent(form.listSearchEl,"click","a",form.formChange),events.attachDesktopEvent(contentHolder,"click",".pagination a",form.pagination),events.attachDesktopEvent(contentHolder,"click",".item a",form.itemClick),events.attachDesktopEvent($(".close-slide-detail"),"click",m2Map.closeDetail),events.attachDesktopEvent(contentHolder,"mouseenter",".item a",form.itemMouseEnter),events.attachDesktopEvent(contentHolder,"mouseleave",".item a",form.itemMouseLeave),events.attachDesktopEvent($window,"resize",form.toggleConditionFields);var ib;form.toggleConditionFields(),desktop.loadedResource();