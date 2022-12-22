import { Col, Row } from 'antd';
import React from 'react';
import { ErrorBoundary } from 'react-error-boundary';
import Container from '../../Components/Container/Container';
import CustomModal from '../../Components/CustomModal/CustomModal';
import ErrorFallback from '../../Components/ErrorFallback/ErrorFallback';
import EventByCategory from '../../Components/EventByCategory/EventByCategory';
import EventByLocation from '../../Components/EventByLocation/EventByLocation';
import EventByMonth from '../../Components/EventByMonth/EventByMonth';
import EventByStatus from '../../Components/EventByStatus/EventByStatus';
import EventByYear from '../../Components/EventByYear/EventByYear';
import GlobalFilterButton from '../../Components/GlobalFilterButton/GlobalFilterButton';
import { eluxTranslation } from '../../Translation/Translation';
import './Events.scss';

function Events() {
    const { eventDashboard } = eluxTranslation;

    return (
        <>
            <h2 className="section-title">{eventDashboard}</h2>
            <Container>
                <Row gutter={20}>
                    <Col span={12}>
                        <ErrorBoundary FallbackComponent={ErrorFallback}>
                            <EventByYear />
                        </ErrorBoundary>
                    </Col>
                    <Col span={12}>
                        <ErrorBoundary FallbackComponent={ErrorFallback}>
                            <EventByLocation />
                        </ErrorBoundary>
                    </Col>
                </Row>
                <Row>
                    <Col span={24}>
                        <hr className="horizontal-bar" />
                        <ErrorBoundary FallbackComponent={ErrorFallback}>
                            <EventByMonth />
                        </ErrorBoundary>
                    </Col>
                </Row>
                <Row>
                    <Col span={24}>
                        <hr className="horizontal-bar" />
                        <ErrorBoundary FallbackComponent={ErrorFallback}>
                            <EventByCategory />
                        </ErrorBoundary>
                    </Col>
                </Row>
                <Row gutter={20}>
                    <Col span={12}>
                        <ErrorBoundary FallbackComponent={ErrorFallback}>
                            <EventByStatus />
                        </ErrorBoundary>
                    </Col>
                    <Col span={12} />
                </Row>
            </Container>
            <ErrorBoundary FallbackComponent={ErrorFallback}>
                <GlobalFilterButton />
            </ErrorBoundary>
            <CustomModal />
        </>
    );
}

export default Events;
