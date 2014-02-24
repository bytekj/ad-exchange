#include <stdio.h>
#include <stdlib.h>
#include <mysql.h>
#define DEBUG		1

void vlcreadfn(void*);

int main(int argc, char **argv)
{
	char cmd[1000];
	int breakNow = 0;
	FILE *fp, *paramsfp;
	char filename[500];
	char nextLine[100];
	int framerate, desiredWidth, desiredHeight, videoBitrate, cabacFlag, h264Profile, refFrames, audioSampRate, audioBitrate, aacProfile;
	
	if(argc != 6)
	{
		printf("Usage: %s <paramsfile> <infile> <is_the_file_ad> <outfile path> <outfile name>\n", argv[0]);
		exit(0);
	}
	//Ensure gst plugin rtpdumpplugin is installed

	paramsfp = fopen(argv[1], "r");
	
	sprintf(filename, "%s", argv[2]);

	fscanf(paramsfp, "%d", &framerate);
	fscanf(paramsfp, "%d", &desiredWidth);
	fscanf(paramsfp, "%d", &desiredHeight);
	fscanf(paramsfp, "%d", &videoBitrate);
	fscanf(paramsfp, "%d", &cabacFlag);
	fscanf(paramsfp, "%d", &h264Profile);
	fscanf(paramsfp, "%d", &refFrames);
	fscanf(paramsfp, "%d", &audioSampRate);
	fscanf(paramsfp, "%d", &audioBitrate);
	fscanf(paramsfp, "%d", &aacProfile);
	fclose(paramsfp);

	
	if(DEBUG)	printf("Creating ts file %s.ts from %s with fps %d, \nw %d, \nh %d, \nbrate %d, \ncabacflag %d, \n264profile %d, \nref# %d, \naud sampling rate %d, \naud brate %d, \naac prof %d\n",filename, filename, framerate, desiredWidth, desiredHeight, videoBitrate, cabacFlag, h264Profile, refFrames, audioSampRate, audioBitrate, aacProfile);

	if(atoi(argv[3]) == 1) //ad enabled
	{
		sprintf(cmd, "gst-launch-0.10 -q filesrc location=%s ! qtdemux name=demux demux. ! queue ! ffdec_h264 ! queue ! videorate ! video/x-raw-yuv,framerate=%d/1 ! videoscale ! 'video/x-raw-yuv, width=%d,height=%d,pixel-aspect-ratio=1/1' ! queue ! textoverlay valign=bottom halign=right text='A D V E R T I S E M E N T' shaded-background=true font-desc='Ubuntu 12' ! queue ! x264enc tune=zerolatency bitrate=%d cabac=%d aud=true profile=%d rc-lookahead=0 ref=%d psy-tune=5 ! queue ! mpegtsmux name=mux ! queue ! filesink location=%s.ts demux. ! queue ! faad ! audioconvert ! audioresample ! 'audio/x-raw-int,rate=%d,channels=2' ! queue ! faac bitrate=%d profile=%d ! queue ! mux.", filename, framerate, desiredWidth, desiredHeight, videoBitrate, cabacFlag, h264Profile, refFrames, filename, audioSampRate, audioBitrate, aacProfile);
	}
	else
	{
  		sprintf(cmd, "gst-launch-0.10 -q filesrc location=%s ! qtdemux name=demux demux. ! queue ! ffdec_h264 ! queue ! videorate ! video/x-raw-yuv,framerate=%d/1 ! videoscale ! 'video/x-raw-yuv, width=%d,height=%d,pixel-aspect-ratio=1/1' ! queue ! x264enc tune=zerolatency bitrate=%d cabac=%d aud=true profile=%d rc-lookahead=0 ref=%d psy-tune=5 ! queue ! mpegtsmux name=mux ! queue ! filesink location=%s.ts demux. ! queue ! faad ! audioconvert ! audioresample ! 'audio/x-raw-int,rate=%d,channels=2' ! queue ! faac bitrate=%d profile=%d ! queue ! mux.", filename, framerate, desiredWidth, desiredHeight, videoBitrate, cabacFlag, h264Profile, refFrames, filename, audioSampRate, audioBitrate, aacProfile);
	}
//	printf("cmd = %s", cmd);

	system(cmd);

	pthread_t vlcread;
	char outfile[100];
	sprintf(outfile, "%s/%s", argv[4], argv[5]);
	pthread_create(&vlcread, NULL, vlcreadfn, (void *)outfile);

	sleep(3); //3 secs sleep

	char vlc_cmd[1000];
	sprintf(vlc_cmd, "cvlc --quiet --play-and-exit %s.ts --sout=#'rtp{mp4a-latm,dst=127.0.0.1,port-video=30000,port-audio=30002}'", filename);
	if(DEBUG)	printf("Creating rtp packets and sending to gst...\n");
	system(vlc_cmd);
	//pthread_join(vlcread, NULL);

	char delcmd[300];
	sprintf(delcmd, "rm %s.ts", filename);
	if(DEBUG)	printf("Deleting ts file...\n");
	system(delcmd);
#if 1
    // check whether the files are ready
    // Udate db
    MYSQL *conn;
    MYSQL_RES *result;
    MYSQL_ROW row;
    int num_fields;
    int i;

    conn = mysql_init(NULL);
    if (conn == NULL) {
        printf("Error %u: %s\n", mysql_errno(conn), mysql_error(conn));
        return 1;
    }   
    if (mysql_real_connect(conn, "localhost", "root", 
                                "sam123", "adex", 0, NULL, 0) == NULL) {
        printf("Error %u: %s\n", mysql_errno(conn), mysql_error(conn));
        return 1;
    }   
    char query[1000];
    sprintf(query, "update content_profiles set encode_status=1 where filename='%s'", argv[5]);
    if(DEBUG)	printf("Query: %s\n", query);
    if (mysql_query(conn, query)) {
        printf("Error %u: %s\n", mysql_errno(conn), mysql_error(conn));
        return 1;
    }   
    result = mysql_store_result(conn);
	if(result==NULL)
		printf("mysql err %s\n", mysql_error(conn));
    /*
    while ((row = mysql_fetch_row(result)) != NULL)
		printf("%s \n", row[0]);
		*/
    /*
    num_fields = mysql_num_fields(result);
    while ((row = mysql_fetch_row(result))){
        for(i = 0; i < num_fields; i++){
            printf("%s ", row[i] ? row[i] : "NULL");
        }   
        printf("\n");
    }   */
    mysql_free_result(result);
    mysql_close(conn);
#endif
}

void vlcreadfn(void* param)
{
	char *filename = (char *)param;

	printf("Inside vlcreadfn... \n");
	char gst_dump[1000];
	sprintf(gst_dump, "gst-launch-0.10 -q udpsrc port=30000 ! queue ! rtpdumpplugin ! queue ! filesink location=%s_vid.rtp udpsrc port=30002 ! queue ! rtpdumpplugin ! queue ! filesink location=%s_aud.rtp", (char *)filename, (char *)filename);
	if(DEBUG)	printf("Receiving rtp packets and dumping...\n");
	system(gst_dump);
}
