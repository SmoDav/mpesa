#############################################################################
# update the db_host, db_user, db_name, db_pass values
# db_get_all only grabs data for stocks included in ../data/Price
# db_get_all outputs a dictionary of dataframes named by the stock ticker (capital)
# eg:
# stocks = db_get_all()
# stocks['AA'] -> dataframe for AA
# stocks['AA']['close'][0] -> 81.2958
# stocks['AA']['date'][0] -> datetime.date(2008, 2, 21)


import MySQLdb as mdb
import pandas as pd
import os
import datetime as dt
from pandas.io import sql


def db_get_ticker(ticker):
    t = ticker
    cols = ['ticker', 'closer', 'date']

    db_host = '216.230.228.88:3306'
    db_user = 'bc8_scottede'#scottdb'
    db_name = 'securities'
    db_pass = 'nycdsa'
    #con = mdb.connect(host = db_host, user = db_user, passwd = db_pass, db=db_name)
    con = mdb.connect(host = db_host, user = db_user, db=db_name)
    query = ("SELECT * FROM stock_data WHERE ticker = '%s'" % (t))
    df = sql.read_sql(query, con=con)
    print "grabbing stock data for %s" % (t)
    return (df)

def db_get_all():
    stocks = []
    pricedir = '../data/Price/' #directory
    for file_ in set(os.listdir(pricedir)):
        try:
            g = len(file_)
            if g < 10:
                stocks.append(str(file_).split(".")[0])
        except Exception, e:
            print e
            continue
    print "getting all stock data: "
    [stock for stock in stocks]
    stock_data = {stock: db_get_ticker(stock) for stock in stocks}

    return (stock_data)
