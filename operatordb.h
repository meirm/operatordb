//
//  operatordb.h
//  operatordb
//
//  Created by Andreas Fink on 23.12.16.
//  Copyright © 2016 Andreas Fink. All rights reserved.
//
// This source is dual licensed either under the GNU GENERAL PUBLIC LICENSE
// Version 3 from 29 June 2007 and other commercial licenses available by
// the author.


void get_operator_from_imsi(const char *imsi,
                            char **country, /* this is a 3 character ISO code */
                            char **organisation,
                            char **network,
                            char **abbreviated_name,
                            char **mcc,
                            char **mnc,
                            char **sim,
                            char **last_update,
                            char **operator_code);

