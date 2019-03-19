// Copyright 1999-2018. Plesk International GmbH. All rights reserved.

import { createStore, combineReducers, compose, applyMiddleware } from 'redux';
import { routerMiddleware } from 'react-router-redux';
import thunk from 'redux-thunk';
import getAppReducer from './reducers/app';

const create = (data, history) => createStore(
    combineReducers({
        app: getAppReducer(data),
    }),
    compose(
        applyMiddleware(
            thunk,
            routerMiddleware(history)
        ),
        window.__REDUX_DEVTOOLS_EXTENSION__ ? window.__REDUX_DEVTOOLS_EXTENSION__() : f => f
    )
);

export default create;
