var basePath = "http://aparca.info/m/"; 
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
var map;
var oldDirections = [];
var currentDirections = null;

var defaultStartPoint = new google.maps.LatLng(60, 105); // @TODO set start point
function initialize() {
    var myLatlng = new google.maps.LatLng();
    var myOptions = {
        zoom: 15,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    initDirections()
    initMap();

    google.maps.event.addListener(map, 'click', function(place) {
        reset();
        setWhereIam(place.latLng);
    });
}

function initDirections() {
    directionsDisplay = new google.maps.DirectionsRenderer({
        map: map,
        preserveViewport: true,
        draggable: true
    });
    directionsDisplay.setPanel(document.getElementById("directions_panel"));

    google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
        if (currentDirections) {
            oldDirections.push(currentDirections);
            setUndoDisabled(false);
        }
        currentDirections = directionsDisplay.getDirections();
    });

    setUndoDisabled(true);
}

var geocoder = new google.maps.Geocoder();
var results;
function geocode(request) {  
  var hash = 'q=' + request.address;
  var language = 'es';
  hash += '&language=' + language;
  request.language = language;

  geocoder.geocode(request, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
          var lat = results[0].geometry.location.lat();
          var lng = results[0].geometry.location.lng();
          
          var latlng = new google.maps.LatLng(lat, lng);
          reset();
          selectedRoute.parking = undefined;
          setWhereIam(latlng);
      }
  });
}

function search(txt) {
    geocode({'address': txt});
    $('#parkings').click();
}

function getGeoName(latLng) {
    var lon = latLng.lng();
    var lat = latLng.lat();
    $.getJSON(basePath + "geo.php", {
        mode: 0,
        lat: lat, 
        lon: lon
    }, function(json){
        $('#search').val(json.formatted_address);
    });
}
var whereIamMarker;
function setWhereIam(latLng){
    initialLocation = latLng;
    getGeoName(latLng);
    if (whereIamMarker) {
        whereIamMarker.setMap(null);
    }
    whereIamMarker = new google.maps.Marker({
        position: initialLocation,
        map: map,
        icon: basePath + 'css/gfx/icons/usuario.png'
    });
    map.setCenter(latLng);
    loadPoints();
}

function initMap() {
    // Try W3C Geolocation method (Preferred)
    if(navigator.geolocation) {
        browserSupportFlag = true;
        navigator.geolocation.getCurrentPosition(function(position) {
            var location = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
            setWhereIam(location);
        }, function() {
            handleNoGeolocation(browserSupportFlag);
        });
    } else if (google.gears) {
        // Try Google Gears Geolocation
        browserSupportFlag = true;
        var geo = google.gears.factory.create('beta.geolocation');
        geo.getCurrentPosition(function(position) {
            var location = new google.maps.LatLng(position.latitude,position.longitude);
            setWhereIam(location);
        }, function() {
            handleNoGeolocation(browserSupportFlag);
        });
    } else {
        // Browser doesn't support Geolocation
        browserSupportFlag = false;
        handleNoGeolocation(browserSupportFlag);
    }
}
function handleNoGeolocation(errorFlag) {
    if (errorFlag == true) {
        var location = defaultStartPoint;
    } else {
        var location = defaultStartPoint;
    }
    setWhereIam(location);
}
var polyline = new google.maps.Polyline({
    path: [],
    strokeColor: '#0000FF',
    strokeWeight: 5
});

function createRoute(lat, lon) {
    var request = {
        origin: initialLocation,
        destination: new google.maps.LatLng(lat, lon),
        travelMode: google.maps.DirectionsTravelMode.DRIVING,
        language: 'es'
    };

    directionsService.route(request, function(response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
            directionsDisplay.setOptions( {
                suppressMarkers: true
            } );
        }
    });
}

function undo() {
    currentDirections = null;
    directionsDisplay.setDirections(oldDirections.pop());
    if (!oldDirections.length) {
        setUndoDisabled(true);
    }
}
function setUndoDisabled(value) {
//document.getElementById("undo").disabled = value;
}

var pois = {};

function getCookie(c_name) {
    var i,x,y,ARRcookies=document.cookie.split(";");
    for (i=0;i<ARRcookies.length;i++) {
        x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
        y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
        x=x.replace(/^\s+|\s+$/g,"");
        if (x==c_name) {
            return unescape(y);
        }
    }
}

function deleteCookie(c_name) {
    setCookie(c_name, '', -1);
}

function setCookie(c_name, value, exdays) {
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}

function _save(c_name, c_value)
{
    setCookie(c_name, c_value, 365);
}
function desCheckIn()
{
    deleteCookie("aparca_id");
    deleteCookie("aparca_date");
    infowindow.close();
    $('#parkings').click();
}

function checkIn(id)
{
    deleteCookie("aparca_id");
    deleteCookie("aparca_date");
    _save("aparca_id", id);
    _save("aparca_date", Number(new Date()));

    $('#gasto').click();
}

var selectedRoute = {};
function loadPoints() {
    var lon = initialLocation.lng();
    var lat = initialLocation.lat();
    var json;
    $.getJSON("aparca.php", {
        lat: lat, 
        lon: lon
    }, function(json){
        $("#listado").empty();			
        $("#parkingTemplate").tmpl(json).appendTo("#listado");
        for (var i=0; i<json.length; i++) {
            pois[i] = placeMark(json[i]);
            if (i == 0 && selectedRoute.parking == undefined) {
                clickOnPOI(json[0].title, json[0].lat, json[0].lon);
            }
        }

        if (selectedRoute.parking) {
            selectParking(selectedRoute.parking, selectedRoute.lat, selectedRoute.lon);
            createRoute(selectedRoute.lat, selectedRoute.lon);
        }
        $('#lista').click();
    });
}

function clickOnPOI2(title, lat, lon) {
    clickOnPOI(title, lat, lon);
    $('#mapa').click();
}

function clickOnPOI(title, lat, lon)
{
    selectParking(title, lat, lon);
    createRoute(lat, lon);
}

var selInfowindow;
function placeMark(elem) {
    var pLat = elem.lat;
    var pLon = elem.lon;
    var parkingName = elem.title;
    
    if (elem.plazasLibres > 0 && elem.plazasLibres <= 10) {
        currentIcon = basePath + 'css/gfx/icons/pocas-plaza.png';
    } else if (elem.plazasLibres > 10){
        currentIcon = basePath + 'css/gfx/icons/con-plaza.png';
    } else if (elem.plazasLibres == 0){
        currentIcon = basePath + 'css/gfx/icons/sin-plaza.png';
    } else {
        currentIcon = basePath + 'css/gfx/icons/sin-info-plaza.png';
    }
    
    var point = new google.maps.Marker({
        position: new google.maps.LatLng(pLat, pLon),
        map: map,
        icon: currentIcon
    });
    elem.currentIcon = currentIcon;
    var content = $("#infoWindow").tmpl(elem).html();
    
    var infowindow = new google.maps.InfoWindow({
        content: content
    });
    google.maps.event.addListener(point, 'click', function(event) {
        selectParking(parkingName, pLat, pLon);
        createRoute(pLat, pLon);
        infowindow.open(map, point);
        selInfowindow = infowindow;
    });

    return point;
}

function selectParking(parkingName, pLat, pLon) {
    selectedRoute.parking = parkingName;
    selectedRoute.lat = pLat;
    selectedRoute.lon = pLon;
}

var currentLayer;

function cleanSelectedParking()
{
    selectedRoute = {};
    reset();
    loadPoints();
}

function clearLayer() {
    if (currentLayer != null) {
        currentLayer.setMap(null);
    }
}

function reset() {
    directionsDisplay.setMap(null);
    initDirections();
    oldDirections = [];
    currentDirections = null;
    document.getElementById("directions_panel").innerHTML = '';
    clearOverlays();
}

function clearOverlays() {
    if (pois) {
        for (i in pois) {
            pois[i].setMap(null);
        }
    }
}

$(function() {
    initialize();
    
    $('#lista').click(function() {
        $("li.nav").removeClass('active');
        $("li#nav-lista").addClass('active');
        $('#content-mapa').hide();
        $('#content-detail').hide();
        
        $('#content-lista').show();
    });
    
    $('#mapa').click(function() {    
        $("li.nav").removeClass('active');
        $("li#nav-mapa").addClass('active');
        $('#content-lista').hide();
        $('#content-detail').hide();
        
        $('#content-mapa').show();
    });
    
    $('#buscar').click(function() {    
        //$("li.nav").removeClass('active');
        //$("li#nav-buscar").addClass('active');

        $('#content-buscar').toggle();
    });
    
    $('#mapa').click(function() {    
        $("li.nav").removeClass('active');
        $("li#nav-mapa").addClass('active');
        $('#content-lista').hide();
        $('#content-detail').hide();
        
        $('#content-mapa').show();
    });
    
    $('#show-detail').click(function() {
        $("li.nav").removeClass('active');
        $("li#nav-lista").addClass('active');
        $('#content-lista').hide();
        $('#content-mapa').hide();
        
        $('#content-detail').show();
    });
    $('#search-submit').click(function() {
        search($('#search').val());
        return false;
    });
    
    $('#hide-detail').click(function() {
        $('#mapa').click();
    });
    
    $('#gasto').click(function() {
        $("a.menu").removeClass('active');
        $("a#gasto").addClass('active');
        $('#sub-menu').hide();
        $('#content-detail').hide();
        $('#content-lista').hide();
        $('#content-mapa').hide();
        
        $('#content-gasto').show();
        var aparcaId = getCookie("aparca_id");
        if (aparcaId) {
            $('#content-gasto').html("");    
            $.getJSON(basePath + "aparca.php", {
                id: aparcaId,
                t: getCookie("aparca_date")
            }, function(json){
                var content = $("#gastoTemplate").tmpl(json).html();
                $('#content-gasto').html(content);
            });
        } else {
            $('#content-gasto').html("<h1>A&uacute;n no has aparcado</h1>");
        }
    });

    $('#parkings').click(function() {
        $("a.menu").removeClass('active');
        $("a#parkings").addClass('active');
        $('#content-gasto').hide();
        $('#content-mapa').show();
        $('#sub-menu').show();
    });
});
