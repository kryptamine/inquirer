// Copyright 1999-2018. Plesk International GmbH. All rights reserved.
import {
    createElement,
    Component,
    PropTypes,
} from '@plesk/ui-library';
import { connect } from 'react-redux';
import ErrorBoundary from './ErrorBoundary';

// This class could be extended by adding a default toaster functionality
class App extends Component {
    render() {
        return (
            <ErrorBoundary>
                <div>
                    {this.props.children}
                </div>
            </ErrorBoundary>
        );
    }
}

App.propTypes = {
    children: PropTypes.any,
};

export default App;
