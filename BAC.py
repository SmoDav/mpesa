
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import csv
import random

# Windows users need to specify the path to chrome driver you just downloaded.
# driver = webdriver.Chrome('path\to\where\you\download\the\chromedriver')

def seekalpha():
	driver = webdriver.Chrome()
	url="file:///Users/gaoxu/Desktop/BAC.htm"

	driver.get(url)


	csv_file = open('BAC.csv', 'wb')
	writer = csv.writer(csv_file)
	#writer.writerow(['date', 'news'])==> main function
	# Page index used to keep track of where we are.


			


# Find all the reviews.
	buffe=0
	newsunit = driver.find_elements_by_xpath('//ul[@class="market_currents_list"]/li[@class="mc_list_li"]')
	for nu in newsunit:
				# Initialize an empty dictionary for each review
		news_dict = {}
				# Use Xpath to locate the title, content, username, date.
		try:
			if buffe==0:
				date = nu.find_element_by_xpath('div[@class="mc_list_texting right bullets"]//span[@class="date pad_on_summaries"]').text
				print date
				news= nu.find_element_by_xpath('div[@class="mc_list_texting right bullets"]//a[@class="market_current_title"]').text
				print news
				content=nu.find_element_by_xpath('div[@class="mc_list_texting right bullets"]/span[@class="general_summary light_text bullets"]//li').text
				print content
		
				news_dict['date'] = date
				news_dict['news'] = news
				news_dict['content'] = content

				writer.writerow(news_dict.values())
				print "Write to csv file"
				# Locate the next button on the page.

				
				print "wating time is ", 3
				time.sleep(3)
			else:
				content = nu.find_element_by_xpath('div[@class="mc_list_texting right old_ver"]/span[@class="general_summary light_text bullets"]/a').text
				news=''
				

		except Exception as e:
			buffe=1

			

	csv_file.close()
	driver.close()
			
			
			

seekalpha()
	