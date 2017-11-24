
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import csv
import random

# Windows users need to specify the path to chrome driver you just downloaded.
# driver = webdriver.Chrome('path\to\where\you\download\the\chromedriver')
def main_function():
	csv_file=open('market.csv','wb')
	csv_file.close()
	print "market.csv created"
	page=range(1,8000,4)
	print "pagenumber created"
	for pagenumber in page:
		everyturn(pagenumber)
		time.sleep(20)

def everyturn(pagenumber):
	driver = webdriver.Chrome()
	url="http://seekingalpha.com/market-news/"+str(pagenumber)

	driver.get(url)

	csv_file = open('market.csv', 'a')
	writer = csv.writer(csv_file)
	#writer.writerow(['date', 'news'])==> main function
	# Page index used to keep track of where we are.
	i=0
	while i<3:
		i=i+1
		try:
			print "Scraping Page number ", pagenumber+i-1
			


			# Find all the reviews.
			newsunit = driver.find_elements_by_xpath('//li[@class="item"]')
			for nu in newsunit:
				# Initialize an empty dictionary for each review
				news_dict = {}
				# Use Xpath to locate the title, content, username, date.
				date = nu.get_attribute('data-last-date')
				news = nu.find_element_by_xpath('h4/a').text
				news_dict['date'] = date
				news_dict['news'] = news
	   			writer.writerow([unicode(s).encode("utf-8") for s in news_dict.values()])
			# Locate the next button on the page.
			button = driver.find_element_by_xpath('//div[@id="pagination"]//li[@class="next"]/a')
			button.click()
			
			print "wating time is ", 3
			time.sleep(3)
		except Exception as e:
			print e
			csv_file.close()
			driver.close()
			break
main_function()
	