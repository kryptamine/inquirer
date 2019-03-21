import { createElement } from '@plesk/ui-library';
import { Route, Switch } from 'react-router-dom';
import App from './components/App';
import NotFound from './components/NotFound';
import Results from './pages/Results';

const routes = (
    <App>
        <Switch>
            <Route path="/top" component={Results} />
            <Route path="*" component={NotFound} />
        </Switch>
    </App>
);

export default routes;
