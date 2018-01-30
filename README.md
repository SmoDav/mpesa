# Event_driven_stock_prediction
This project is about event-driven stock prediction. It analyzes the impact of news to the stock price movement. 

# Model Development
Basic Model: Neural Network with 2 hidden layers.
1st hidden layer: 300 nodes, tanh 
2nd hidden layer: 50 nodes, tanh
output layer: 1 node, sigmoid

Version 1: S&P500, NYSE Volume, Nasdaq Composite, 5-day lag time series for the stock

Version 2: Version 1 + Sentiment Polarity for news

Version 3: Version 1 + average word embeddings for the most confidencial sentence(filter by ReVerb, http://reverb.cs.washington.edu/)

Version 4: Version 1 + Doc2Vec(all news content)


# Conclusion
News actually can improve the performance of stock predictions.
But it is hard to decide the best model based on the facts below:
1. Not all news important
2. Noisy information in single news
3. No perfect way to filter and find the most useful sentence in single news
4. Some news has impact in long period not in short.


# Authors:
Xu Gao: the leader, initial research, news parsing, model design

Scott Edenbaum: SQL database created and BeautifulSoup scraping
