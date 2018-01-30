
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import csv
import random

driver = webdriver.Chrome()

driver.get("http://seekingalpha.com/market-news/308")

csv_file = open('../data/news-p300.csv', 'wb')
writer = csv.writer(csv_file)
writer.writerow(['date', 'news'])
# Page index used to keep track of where we are.
index = 1
while True:
	try:
		print "Scraping Page number ", index
		index = index + 1


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
		T=random.randint(1, 5)
		print "wating time is ", T
		time.sleep(T)
	except Exception as e:
		print e
		csv_file.close()
		driver.close()
		
		break

	
