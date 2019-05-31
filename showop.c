#include <operatordb.h>
#include <stdio.h>

char *country="   "; /* this is a 3 character ISO code */
char *organisation=    "                                ";
char *network=         "                                ";
char *abbreviated_name="                                ";
char *mcc=             "                                ";
char *mnc=	       "                                ";
char *sim=             "                                ";
char *last_update=     "                                ";
char *operator_code=   "                                ";

int main(int argc, char ** argv){
	get_operator_from_imsi(argv[1],
                            &country, /* this is a 3 character ISO code */
                            &organisation,
                            &network,
                            &abbreviated_name,
                            &mcc,
                            &mnc,
                            &sim,
                            &last_update,
                            &operator_code);
	printf("{ \"country\": \"%s\",",country);
	printf(" \"network\": \"%s\",",network);
	printf(" \"abbreviated_name\": \"%s\"",abbreviated_name);
	printf("}\n");
	return 0;
}
