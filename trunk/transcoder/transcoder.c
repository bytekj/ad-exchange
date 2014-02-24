        #include <stdio.h>
#include<mysql.h>

int main(){
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

  	if (mysql_query(conn, "select * from ad")) {
      		printf("Error %u: %s\n", mysql_errno(conn), mysql_error(conn));
		return 1;
  	}
	result = mysql_store_result(conn);

	num_fields = mysql_num_fields(result);

	while ((row = mysql_fetch_row(result)))
	{
		for(i = 0; i < num_fields; i++)
		{
			printf("%s ", row[i] ? row[i] : "NULL");
		}
		printf("\n");
	}

	mysql_free_result(result);


  	mysql_close(conn);

	return 0;
}
