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
	for each_news in news:
		#delimiters=".","?","!","\n\n"
		#regexPattern = '|'.join(map(re.escape, delimiters))
		#news_list=re.split(regexPattern,each_news)
		#print len(news_list)
		#news=filter(lambda x: (ticker in x) or (company_name in x), news_list)

	
		news=each_news+'.'
		print news
		f=open("single_news.txt",'w')
		f.write(news)
		f.close()
		os.system("java -Xmx512m -jar reverb-latest.jar single_news.txt > output.txt")
		output=open("output.txt",'r')
		info=output.read()
		output.close()
		print "reverb finished, then parse"
		confid,subj,obje,verb_=reverb_parse(info)
		confidence.append(confid)
		subject.append(subj)
		object_.append(obje)
		verb.append(verb_)

		
	os.system("rm single_news.txt")
	os.system("rm output.txt")
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

