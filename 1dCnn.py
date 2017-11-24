#!/usr/bin/env python

from __future__ import print_function, division

import numpy as np
from keras.layers import Convolution1D, Dense, MaxPooling1D, Flatten
from keras.models import Sequential
import pandas as pd

def make_timeseries_regressor(window_size, filter_length, nb_input_series=1, nb_outputs=1, nb_filter=4):

    model = Sequential((

        Convolution1D(nb_filter=nb_filter, filter_length=filter_length, activation='relu', input_shape=(nb_input_series)),
        MaxPooling1D(),     # Downsample the output of convolution by 2X.
        Convolution1D(nb_filter=nb_filter, filter_length=filter_length, activation='relu'),
        MaxPooling1D(),
        Flatten(),
        Dense(nb_outputs, activation='sigmoid'),     # For binary classification, change the activation to 'sigmoid'
    ))
    #model.compile(loss='mse', optimizer='adam', metrics=['mae'])
    # To perform (binary) classification instead:
    model.compile(loss='binary_crossentropy', optimizer='adam', metrics=['binary_accuracy'])
    return model


def evaluate_timeseries(df_x,y, window_size):
    """Create a 1D CNN regressor to predict the next value in a `timeseries` using the preceding `window_size` elements
    as input features and evaluate its performance.
    :param ndarray timeseries: Timeseries data with time increasing down the rows (the leading dimension/axis).
    :param int window_size: The number of previous timeseries values to use to predict the next.
    """
    filter_length = 3
    nb_filter = 1
    df_x = np.atleast_2d(df_x)
    if df_x.shape[0] == 1:
        df_x = df_x.T       # Convert 1D vectors to 2D column vectors

    nb_samples, nb_series = df_x.shape
    print('\n\nTimeseries ({} samples by {} series):\n'.format(nb_samples, nb_series), df_x)
    model = make_timeseries_regressor(window_size=window_size, filter_length=filter_length, nb_input_series=1, nb_outputs=1, nb_filter=nb_filter)
    print('\n\nModel with input size {}, output size {}, {} conv filters of length {}'.format(model.input_shape, model.output_shape, nb_filter, filter_length))
    model.summary()

    test_size = int(0.2 * nb_samples)           # In real life you'd want to use 0.2 - 0.5
    df_x = np.atleast_3d(df_x)
    X_train, X_test, y_train, y_test = df_x[:-test_size], df_x[-test_size:], y[:-test_size], y[-test_size:]
    model.fit(X_train, y_train, nb_epoch=25, batch_size=1, validation_data=(X_test, y_test))

    pred = model.predict(X_test)
    print('\n\nactual', 'predicted', sep='\t')
    for actual, predicted in zip(y_test, pred.squeeze()):
        print(actual, predicted, sep='\t')

def main():
    df=pd.read_csv('table.csv').dropna()
    df_X=df.iloc[:,1:5]
    y=map(lambda x: 1 if x>0 else 0,df.iloc[:,5])
    Vec_d=4
    evaluate_timeseries(df_X,y,Vec_d)



    #####download data and import


main()
