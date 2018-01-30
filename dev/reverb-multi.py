###This file is used to use reverb function on given datasets###
###it creates text file and run in terminal then delete that file####

import os
import pandas as pd
import numpy as np
from nltk import tokenize
import re
from sentiment import parse_1

def reverb():
	ticker, df=parse_1()
	news=df.content
	confidence=[]
	subject=[]
	object_=[]
	verb=[]
	
	single_news = "single_news_" + ticker + ".txt"
	ticker_output = "output_" + ticker + ".txt"
	for each_news in news:
		news=each_news+'.'
		print news
		f=open(single_news,'w')
		f.write(news)
		f.close()
		javaosarg = "java -Xmx512m -jar reverb-latest.jar " + single_news + " > " + ticker_output
		#os.system("java -Xmx512m -jar reverb-latest.jar single_news.txt > output.txt")
		os.system(javaosarg)
		output=open(ticker_output,'r')
		info=output.read()
		output.close()
		print "reverb finished, next parse"
		confid,subj,obje,verb_=reverb_parse(info)
		confidence.append(confid)
		subject.append(subj)
		object_.append(obje)
		verb.append(verb_)
		
	osrmarg1 =  "rm " + single_news
	osrmarg2 = "rm " + ticker_output
	os.system(osrmarg1)
	os.system(osrmarg2)
	df['confidence']=confidence
	df['subject']=subject
	df['object']=object_
	df['verb']=verb
	file_='../data/reverb/'+ticker+'reverb.csv'
	df.to_csv(file_)
	
def reverb_parse(info):
	reverb_list=info.split('\n')
	confi=[]
	sub=[]
	obj=[]
	verb=[]
	if len(reverb_list)==0:
		confidence=0
		subject=''
		verb_=''
		object_=''
	else:
		for i in range(len(reverb_list)-1):
			buf=reverb_list[i].split('\t')
			confi.append(float(buf[11]))
			sub.append(buf[-3])
			verb.append(buf[-2])
			obj.append(buf[-1])
		if len(confi)==0:
			confidence=0
			subject=''
			verb_=''
			object_=''
		else:
			idx=np.argmax(confi)
			confidence=confi[idx]
			subject=sub[idx]
			object_=obj[idx]
			verb_=verb[idx]
			print "finish this news"
	return confidence,subject,object_,verb_

reverb()

