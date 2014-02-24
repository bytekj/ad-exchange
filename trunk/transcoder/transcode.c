/* 
 * Copyright (c) 2012 - 2017 NOVIX Media Technologies Private Ltd	
 */


#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <memory.h>
#include <string.h>
#include <signal.h>
#include <time.h>
#include <errno.h>
#include <json/json.h>

#define DEBUG 1

typedef struct {
    unsigned int id;
    unsigned int dur;       // seconds

    unsigned int w;
    unsigned int h;
    int fps;

    unsigned int hz ;           // Hz
    unsigned int ch;

    char *vcodec;
    char *acodec;

    unsigned int cabac;
	unsigned int vid_profile;
	
    char *par;

    unsigned int clip_brate;
    unsigned int vid_brate;
    unsigned int aud_brate;

    char *aspect;
}mp4params;


char logg[500];

void logString(char *log_desc) {

    FILE *flog = fopen("/var/log/transcode.log", "a") ;
    if(flog == NULL){
            printf("Could not open logfile...Exiting...\n");
            exit(0);
    }
    time_t mytime;
    mytime = time(NULL);

    char *tt = ctime(&mytime);
    int ttsize = strlen(tt);
    tt[ttsize -1] = '\t';
    fprintf(flog,"%s%s", tt, log_desc) ;
    if(DEBUG)   printf("%s", log_desc) ;
    fclose(flog) ;
    return ;
}

char* StrStr(char *str, char *target) {

    if (!*target) return str;
    char *p1 = (char*)str, *p2 = (char*)target;
    char *p1Adv = (char*)str;
    while (*++p2)
        p1Adv++;
    while (*p1Adv) {
        char *p1Begin = p1;
        p2 = (char*)target;
        while (*p1 && *p2 && *p1 == *p2) {
            p1++;
            p2++;
        }
        if (!*p2)
            return p1Begin;
        p1 = p1Begin + 1;
        p1Adv++;
    }
    return NULL;
}

mp4params *parseconf_db(char *fileData)
{
	printf("fdata = %s\n", fileData);

	mp4params *prof;
    struct json_object *new_obj, *new_basic_obj;
    new_basic_obj = json_tokener_parse(fileData);
    //struct array_list *arr = json_object_get_array(new_obj);
    prof = malloc(1*sizeof(mp4params));
    char *vreso = malloc(1*sizeof(char *)) ;
    char *ch = malloc(1*sizeof(char *)) ;
	
	new_obj = json_object_object_get(new_basic_obj, "id");
	prof->id = json_object_get_int(new_obj);
    if(DEBUG) printf("prof id: %d\n", prof->id);
    
    new_obj = json_object_object_get(new_basic_obj, "video_resolution");
    vreso = (char *)json_object_get_string(new_obj);
    if(DEBUG)    printf("vid reso: %s\n", vreso);
    char *w = StrStr(vreso, "x") ;
    *w = '\0' ;
    prof->w = atoi(vreso) ;
    if(DEBUG) printf("vid width: %d\n", prof->w) ;
    w++ ;
    prof->h = atoi(w) ;
    if(DEBUG) printf("vid height: %d\n", prof->h) ;
    new_obj = json_object_object_get(new_basic_obj, "video_bit_rate");
    prof->vid_brate = json_object_get_int(new_obj);
    if(DEBUG) printf("vid bitrate: %d\n", prof->vid_brate);
    new_obj = json_object_object_get(new_basic_obj, "video_codec");
    prof->vcodec = (char *)json_object_get_string(new_obj);
    if(DEBUG) printf("vid codec: %s\n", prof->vcodec);
    new_obj = json_object_object_get(new_basic_obj, "video_fps");
    prof->fps = json_object_get_int(new_obj);
    if(DEBUG) printf("vid fps: %d\n", prof->fps);

	/*new_obj = json_object_object_get(new_basic_obj, "pixel_aspect_ratio");
	prof->par = json_object_get_string(new_obj);
	if(DEBUG) printf("par = %s\n", prof->par);*/
	prof->par = "1/1"; //hardcoded	

    new_obj = json_object_object_get(new_basic_obj, "audio_bit_rate");
    prof->aud_brate = json_object_get_int(new_obj);
    prof->aud_brate *= 1000;
    if(DEBUG) printf("aud bitrate: %d\n", prof->aud_brate);

    new_obj = json_object_object_get(new_basic_obj, "audio_sampling_rate");
    prof->hz = json_object_get_int(new_obj);
    if(DEBUG) printf("aud sampling rate: %d\n", prof->hz);
    new_obj = json_object_object_get(new_basic_obj, "audio_channels");
    ch = (char *)json_object_get_string(new_obj);
    if(DEBUG) printf("aud channels: %s\n", ch);
    if(!strncmp(ch, "stereo", 6))
       prof->ch = 2 ;
    else
       prof->ch = 1 ;
    new_obj = json_object_object_get(new_basic_obj, "audio_codec");
    prof->acodec = (char *)json_object_get_string(new_obj);
    if(DEBUG) printf("aud codec: %s\n", prof->acodec);


	//todo: this needs to be read from json. Temp hardcoded
	prof->cabac = 0;
	prof->vid_profile = 1; //baseline

    //if(DEBUG) printf("\n") ;
    free(ch);
    free(vreso);
    return(prof) ;
}

void transcode(mp4params *prof, char *infilename, char *outfilename) {

	char cmd[5000], movecmd[500], dumpcmd[500];
	printf("infilename = %s out = %s par = %s\n", infilename, outfilename, prof->par);
//	sprintf(cmd, "gst-launch-0.10 filesrc location=%s ! queue ! decodebin name=dec ! queue ! videorate skip-to-first=true ! video/x-raw-yuv,framerate=%f/1 ! queue ! videoscale ! video/x-raw-yuv,width=%d,height=%d,pixel-aspect-ratio=%s ! queue ! x264enc tune=zerolatency bitrate=%s cabac=%d aud=true profile=%d rc-lookahead=0 ref=1 ! queue ! mux. dec. ! queue ! audioconvert ! audioresample ! audio/x-raw-int,rate=%d,channels=%d ! queue ! faac bitrate=%d profile=2 outputformat=0 ! queue ! mux. flvmux name=mux streamable=true ! queue ! filesink location=%s", infilename, prof->fps, prof->w, prof->h, prof->par, prof->vid_brate, prof->cabac, prof->vid_profile, prof->hz, prof->ch, prof->aud_brate, outfilename);

	sprintf(cmd, "gst-launch-0.10 filesrc location=%s ! queue ! decodebin name=dec ! queue ! videorate skip-to-first=true ! video/x-raw-yuv,framerate=%d/1 ! queue ! videoscale ! video/x-raw-yuv,width=%d,height=%d,pixel-aspect-ratio=1/1 ! queue ! x264enc tune=zerolatency bitrate=%d cabac=0 aud=true profile=1 rc-lookahead=0 ref=1 ! queue ! mux. dec. ! queue ! audioconvert ! audioresample ! audio/x-raw-int,rate=%d,channels=%d ! queue ! faac bitrate=%d profile=2 outputformat=0 ! queue ! mux. flvmux name=mux streamable=true ! queue ! filesink location=%s", infilename, prof->fps, prof->w, prof->h, prof->vid_brate, prof->hz, prof->ch, prof->aud_brate, outfilename);
	printf("cmd = %s\n", cmd);
	system(cmd);
	//sprintf(movecmd, "mv %s /usr/local/WowzaMediaServer/content/", outfilename);
	//system(movecmd);
	//sprintf(dumpcmd, "rtmpdump -r \"rtmp://127.0.0.1/vod/%s\" -o %s", outfilename, outfilename);
	//system(dumpcmd);
}

int main(int argc, char **argv)
{
	if(argc != 4)
	{	
		printf("Usage: %s <json_string> <input filename with absolute path> <output file name with absolute path>\n", argv[0]);		
		return 0;
	}
	mp4params *params = NULL;

	FILE *fp = fopen(argv[2], "rb");
	int cur_pid = 0;
	char cmd[500];

	cur_pid = (int)getpid();
	
	sprintf(cmd, "cpulimit -p %d -l 10", cur_pid);

	system(cmd);

	if(fp == NULL)
	{
		printf("Input video file not found\n");
		exit(0);
	}

	params = parseconf_db(argv[1]);
	transcode(params, argv[2], argv[3]);
	
	return 0;
}

