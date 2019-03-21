// Copyright 1999-2018. Plesk International GmbH. All rights reserved.
import * as constant from '../helpers/constants';
import { getAppInitialState } from '../helpers/reducers';

const getAppReducers = data => {
    const initialState = getAppInitialState();
    if (data && data.participants) {
        initialState.participants = [...data.participants];
    }
    return (state = initialState, action) => {
        switch (action.type) {
            case constant.GET_RESULTS:
                return {
                    ...state,
                    participants: action.participants,
                };
            default:
                return state;
        }
    };
};

export default getAppReducers;
