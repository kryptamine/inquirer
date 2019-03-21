// Copyright 1999-2018. Plesk International GmbH. All rights reserved.
import getAppReducer from '../app';
import { getAppInitialState } from '../../helpers/reducers';

test('should return initial state', () => {
    expect(getAppReducer()(undefined, {}))
        .toEqual(getAppInitialState());
});
