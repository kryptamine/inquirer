/* eslint-disable react/prefer-stateless-function */
import {
    createElement,
    PropTypes,
    Component,
    Paragraph,
    Layout,
    Text,
    Fragment,
    Tabs,
    Tab,
    Icon,
    List,
} from '@plesk/ui-library';
import { connect } from 'react-redux';
import { QUIZ_PHP, QUIZ_JS, QUIZ_QA } from  '../helpers/constants';

class Results extends Component {
    constructor(props) {
        super(props);
        this.state = {
            activeTab: 0,
        };
    }

    getParticipantsByQuizType = (participants, type) => {
        const filteredItems = [];
        participants.forEach((participant, index) => {
            if (!(type in participant)) {
                return;
            }
            const { score, time, correctAnswerCount } = participant[type];
            const emailParts = participant.email.split('@');
            if (emailParts.length !== 2) {
                return;
            }
            const email = `${emailParts[0]}***`;
            filteredItems.push({
                key:`participant_${index}`,
                email: email,
                score,
                time,
                correctAnswerCount,
            });
        });
        return filteredItems;
    };

    getColumns = () => [{
        key: 'email',
        title: 'Электронный адрес',
        sortable: true,
        width: '25%',
    }, {
        key: 'score',
        title: 'Количество баллов',
        sortable: true,
        width: '25%',
    }, {
        key: 'time',
        title: 'Время прохождения',
        sortable: true,
        width: '25%',
    }, {
        key: 'correctAnswerCount',
        title: 'Количество ответов с начислением баллов',
        sortable: true,
        width: '25%',
    }];

    getTabs = (participants) => {
        const tabs = [];
        [QUIZ_PHP, QUIZ_JS, QUIZ_QA].forEach((type, index) => {
            const title = type.charAt(0).toUpperCase() + type.slice(1);
            const icon = `/images/${type}_logo.png`;
            tabs.push(
                <Tab key={index} title={title} icon={<Icon src={icon} />}>
                    <List
                        columns={this.getColumns()}
                        data={this.getParticipantsByQuizType(participants, type)}
                        sortColumn="score"
                        sortDirection="DESC"
                    />
                </Tab>
            );
        });
        return tabs;
    };

    render() {
        return (
            <Layout
                type={{
                    name: 'fluid',
                    contentWidth: 'lg',
                }}
                header={
                    <Fragment>
                        <img style={{ marginRight: '10px', marginLeft: '10px' }} src="/images/logo.png" />
                        <Text style={{ color: 'white', fontSize: 'x-large' }} >Quiz</Text>
                    </Fragment>
                }
            >
                <Paragraph />
                <Tabs active={this.state.activeTab} >
                    {this.getTabs(this.props.participants)}
                </Tabs>
            </Layout>

        );
    }
}

Results.propTypes = {
    participants: PropTypes.arrayOf(PropTypes.shape({
        email: PropTypes.string.isRequired,
        php: PropTypes.shape({
            score: PropTypes.number.isRequired,
            time: PropTypes.string.isRequired,
            correctAnswerCount: PropTypes.number.isRequired,
        }),
        js: PropTypes.shape({
            score: PropTypes.number.isRequired,
            time: PropTypes.string.isRequired,
            correctAnswerCount: PropTypes.number.isRequired,
        }),
        qa: PropTypes.shape({
            score: PropTypes.number.isRequired,
            time: PropTypes.string.isRequired,
            correctAnswerCount: PropTypes.number.isRequired,
        }),
    })),
};

const mapStateToProps = state => ({
    participants: state.app.participants,
});

export default connect(mapStateToProps, null)(Results);
