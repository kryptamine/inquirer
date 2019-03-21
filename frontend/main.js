import { render, createElement } from '@plesk/ui-library';
import { getAppContainer } from './helpers/container';
import { createBrowserHistory } from 'history';
import { Provider } from 'react-redux';
import { ConnectedRouter } from 'react-router-redux';
import routes from './routes';
import create from './store';

const data = window.__INITIAL_STATE__;
const history = createBrowserHistory({
    basename: '/',
});
const store = create(data, history);

render(
    <Provider store={store}>
        <ConnectedRouter history={history}>
            {routes}
        </ConnectedRouter>
    </Provider>,
    getAppContainer(document.getElementById('root')),
);

