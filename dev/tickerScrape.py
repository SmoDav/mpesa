from sys import version_info
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import csv
import random
import lxml
from bs4 import BeautifulSoup

print "importing libraries..."

# Windows users need to specify the path to chrome driver you just downloaded.
# driver = webdriver.Chrome('path\to\where\you\download\the\chromedriver')

def seekalpha(ticker):
	#py3 = version_info[0] > 2 #creates boolean value for test that Python major version > 2
	#if py3:
	#	file = input("Please Input Ticker: ")
	#else:
	#	file = raw_input("Please Input Ticker: ")
	file = ticker	
	file_f = '../data/' + file + '.csv'
		
	driver = webdriver.Chrome()
	url="http://seekingalpha.com/symbol/" + file + "/news"
	print "setting up url..."
	driver.get(url)
	
	lenOfPage = driver.execute_script("window.scrollTo(0, document.body.scrollHeight);var lenOfPage=document.body.scrollHeight;return lenOfPage;")
	match=False
	i=1
	
	with open(file_f, 'w') as f:
		f.write("Date|")
		f.write("Headline|")
		while match==False and i<10000:
			soup = BeautifulSoup(driver.page_source, 'lxml')
			news = soup.findAll("li",{"class":"mc_list_li"})
			try:
				#print news[~0].contents[1].text.strip('\n')
				print news[~0].contents[3].text.strip('\n').split("|")[0].strip() + '\n'
				#print news[~0].contents[3].contents[1].text.strip('\n').strip() + '\n'
				#print 'length of news[~0]... ' + len(news[~0].contents)
				
			except Exception, e:
				e
				print "error..."
				print e
				continue
			i=i+1
			lastCount = lenOfPage
			time.sleep(1)
			lenOfPage = driver.execute_script("window.scrollTo(0, document.body.scrollHeight);var lenOfPage=document.body.scrollHeight;return lenOfPage;")
			if lastCount==lenOfPage:
				print '-'*120
				print 'FULL LIST' + '\n\n\n'
			
				#for i in len(news):
				x = 0
				N = len(news)
				while (x < N):
					#print news[x].contents[1].text.strip('\n')
					#print news[x].contents[3].contents[1].text.strip('\n').strip() + '\n'
					print news[x].contents[3].text.strip('\n').split("|")[0].strip() + '\n'
					f.write(news[x].contents[1].text.encode('utf-8').strip('\n')+"|")
					#f.write(news[x].contents[3].contents[1].text.encode('utf-8').strip('\n').strip()+"|")
					f.write(news[x].contents[3].text.encode('utf-8').strip('\n').split("|")[0].strip() + "|")
					x +=1
					if x == N:
						break
				print '*'*120 + '\nComplete!\n'
				print str(N) + " News entries for " + file
			
				match=True
	
#		csv_file = open(file_f, 'wb')
#		writer = csv.writer(csv_file)
		#writer.writerow(['date', 'news'])==> main function
		# Page index used to keep track of where we are.


# Find all the reviews.
# 	newsunit = driver.find_elements_by_xpath('//li[@class="mc_list_li"]')
# 	for nu in newsunit:
# 				Initialize an empty dictionary for each review
# 		news_dict = {}
# 				Use Xpath to locate the title, content, username, date.
# 		try:
# 			date = nu.find_element_by_xpath('//span[@class="date"]').text
# 			news= nu.find_element_by_xpath('//a[@class="market_current_title"]').text
# 	
# 			news_dict['date'] = date
# 			news_dict['news'] = news
# 
# 			writer.writerow([unicode(s).encode("utf-8") for s in news_dict.values()])
# 			print "Write to csv file"
# 			Locate the next button on the page.
# 
# 			
# 			print "wating time is ", 3
# 			time.sleep(3)
# 		except Exception as e:
# 			print e
# 			break
# 			csv_file.close()
# 			driver.close()

def main():
	tickers = ['BAC','RF','C','CHK','XOM','SLB','FCX','DOW','AA','VZ','VOD','T','AAPL','JNPR','AMAT','JNJ','LLY','ABT','GE','CAT','LMT','MCD','F','BBBY','PG','KO','K','SO','D','PPL']
	count = 1
	for ticker in tickers:
		print str(count) + " of 30"
		print "Grabbing stock news for: " + ticker
		print '-'*200 + '\n' + '-'*200
		seekalpha(ticker)
		count += 1
	print '*'*200 +'\n' + '-'*200 + '\nComplete!'
	
if __name__ == "__main__":
	main()