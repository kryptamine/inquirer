// Copyright 1999-2018. Plesk International GmbH. All rights reserved.
import {
    createElement,
    Component,
    PropTypes,
    Button,
    Translate,
} from '@plesk/ui-library';

class ErrorBoundary extends Component {
    state = {
        hasError: false,
    };

    componentDidCatch(error) {
        this.setState({ hasError: error.message });
    }

    render() {
        if (this.state.hasError) {
            // You can render any custom fallback UI
            return (
                <div>
                    <h1>
                        {'Error!'}
                    </h1>
                    <p>{this.state.hasError}</p>
                    <p>
                        <Button
                            intent="primary"
                            onClick={() => this.setState({ hasError: false })}
                        >
                            {'Refresh'}
                        </Button>
                    </p>
                </div>
            );
        }
        return this.props.children;
    }
}

ErrorBoundary.propTypes = {
    children: PropTypes.node,
};

ErrorBoundary.defaultProps = {
    children: null,
};

export default ErrorBoundary;
