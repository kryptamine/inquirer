// Copyright 1999-2018. Plesk International GmbH. All rights reserved.
export const getAppContainer = container => {
    const appContainer = document.createElement('div');
    container.appendChild(appContainer);
    return appContainer;
};
