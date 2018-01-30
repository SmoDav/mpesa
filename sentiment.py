### this file is used to add sentiment polarity in existing dataframe
### input: pandas dataframe with date, news title, news content 

import pandas as pd
from textblob import TextBlob
import sys  
import datetime as dt
from dateutil.parser import parse
reload(sys)  
sys.setdefaultencoding('utf8')
def senti():
	ticker, data=parse_1()
	Polarity = map(lambda x: TextBlob(x),data.content)
	result=[]
	for i in range(len(Polarity)):
		k = Polarity[i].sentiment.polarity
		result.append(k)
	data['polarity']=result
	df=date_transform(data)
	####################transform date to standard structure###############
	return ticker,data

def parse_1():
	Ticker=raw_input('please input ticker: ')
	data=open('../data/'+Ticker+'.csv')
	array=data.read().split('|')
	date=[]
	content=[]
	d=0
	for i in range(2,len(array)-1):
		if d==0:
			date.append(array[i])
			d=1
		else:
			content.append(array[i])
			d=0
	dic={}
	dic['Date']=date
	print "date length",len(date)
	dic['content']=content
	print "content length", len(content)
	df=pd.DataFrame(dic)
	return Ticker,df

def date_transform(df):
	date=df.Date
	y=range(2007,2017)
	year=map(lambda x: str(x), y)

	
	real_date=[]
	for i in range(len(date)):
		if any(word in date[i] for word in year):
			d=date[i].split(',')[0]+date[i].split(',')[1]
			dat=parse(d).strftime('%-m/%-d/%y')
		####has year#####
			real_date.append(dat)
		else:
			d=date[i].split(',')[1]+' 2017'
			dat=parse(d).strftime('%-m/%-d/%y')
			real_date.append(dat)
	j=0
	while ':' in real_date[j]:
		j=j+1
	df.Date=real_date
	df=df.iloc[j:,:]
	return df



