<?php

$url = "http://54.243.237.61/adex/public/getad.php?proto=RTSP&cid=1613351931&ip=127.0.0.1&chname=shop50.sdp&t=1234&dev=android&path=/usr/local/WowzaMediaServer/content/&ssid=1234&&pubid=zenga_pub&profid=3&n=1&ua=jwplayer_vast";
$json = file_get_contents($url);

$decoded_json = json_decode($json);
/*
echo "<pre>";
print_r($decoded_json);
echo "</pre>";
*/

$xml = '<?xml version="1.0" encoding="UTF-8"?><VAST xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="vast.xsd" version="2.0"><Ad id="d1d4f7a6-f53f-4028-9225-02268acb9789"><InLine><AdSystem>Adform</AdSystem><AdTitle>In Stream</AdTitle><Creatives><Creative><Linear><Duration>00:00:00</Duration>';
$TrackingEvents = '<TrackingEvents><Tracking event="Complete"><![CDATA[http://54.243.237.61/adex/public/log.php?id='.$decoded_json->ads[0]->id.'&debug=jwplayer_vast]]></Tracking></TrackingEvents>';
$media= '<MediaFiles><MediaFile delivery="progressive" type="video/x-flv" bitrate="#Please enter bitrate value#" width="320" height="240" scalable="false" maintainAspectRatio="false">http://54.243.237.61/adex/resource/ads/encoded/'.$decoded_json->ads[0]->f.'</MediaFile></MediaFiles></Linear></Creative></Creatives></InLine></Ad></VAST>';
//echo $xml.$TrackingEvents.$media;

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<VAST xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="vast.xsd" version="2.0">
        <Ad id="8149558">
            <InLine>
                <AdSystem>ADTECH</AdSystem>
                <AdTitle>ADTECH AD AdId=8149558, CreativeId=109339, AssetId=103109937</AdTitle>
                <Impression><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_START;imptype=2;refsequenceid=2958327165]]></Impression>

                <Creatives>
                    <Creative id="109339">
    <Linear>
<Duration>00:00:14</Duration>
<TrackingEvents>
<Tracking event="acceptInvitation"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_ACCEPTINVITATION;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="close"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_CLOSE;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="collapse"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_COLLAPSE;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="complete"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_END;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="creativeView"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_CREATIVEVIEW;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="expand"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_EXPAND;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="firstQuartile"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_25;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="fullscreen"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_FULLSCREEN;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="midpoint"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_MID;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="mute"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_MUTE;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="pause"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_PAUSE;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="replay"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_REPLAY;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="resume"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_RESUME;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="rewind"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_REWIND;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="stop"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_STOP;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="thirdQuartile"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_75;imptype=2;refsequenceid=2958327165]]></Tracking>
<Tracking event="unmute"><![CDATA[http://ad.ripplead.com/rmevent/3/1333/4043650/0/0/AdId=8149558;CreativeId=109339;BnId=1;rmeventtype=VID_UNMUTE;imptype=2;refsequenceid=2958327165]]></Tracking>
</TrackingEvents>

<VideoClicks>
    <ClickThrough><![CDATA[http://ad.ripplead.com/adlink/1333/4043650/0/3577/AdId=8149558;BnId=1;itime=587130638;nodecode=yes;link=]]></ClickThrough>
    
</VideoClicks>
<MediaFiles>
<MediaFile delivery="progressive" bitrate="0" width="960" height="540" type="video/mp4">
<![CDATA[http://ripplead.s3.amazonaws.com/ads/in/tata/Microsoft/storm8.mp4]]>
</MediaFile>
</MediaFiles>

    </Linear>
</Creative>

                    
                    
                </Creatives>
				
            </InLine>
        </Ad>
</VAST>';
/*<![CDATA[http://54.243.237.61/adex/resource/ads/encoded/'.$decoded_json->ads[0]->f.']]>*/
echo $xml;
?>
