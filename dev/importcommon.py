import MySQLdb as mdb
import pandas as pd
import os
import datetime as dt


def db_send_common():
    db_host = 'betapi.local'
    db_user = 'scottdb'#'scottdb'
    db_pass = 'nycdsa'#'nycdsa'
    db_name = 'securities'#'securities'
    con = mdb.connect(host=db_host, user=db_user, passwd=db_pass, db=db_name)

   # con = mdb.connect(host=db_host, user=db_user,passwd=db_pass db=db_name)
    df_ = pd.read_csv('../data/Price/common_data.csv')
    df_.columns = ['date', 'spx', 'nysevol', 'ccmp']
    cols = ['spx', 'nysevol', 'ccmp', 'date']
    df_ = df_[cols]
    with con:
        cur = con.cursor()
        cur.execute("CREATE TABLE common_data ( SPX decimal(19,10) NULL, NYSEVOL decimal(19,10) NULL, CCMP decimal(19,10) NULL, date date NOT NULL );")
        print 'creating table common_data ...'
        for i in range(2285):
            spx_r, nysevol_r, ccmp_r, dat_r = df_.ix[i]
            insert_str = "(" + str(spx_r) + ", " + str(nysevol_r) + ", "  + str(ccmp_r) + ", STR_TO_DATE(" + "'" + str(dat_r) + "'" + ",'%c/%e/%y'));"
            cur.execute( "INSERT INTO common_data VALUES " + insert_str )
            print "inserting record: %d" % (i)

def main():
    db_send_common()


if __name__ == "__main__":
    main()
