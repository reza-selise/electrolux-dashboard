import { Col, Row } from 'antd';
import React from 'react';
import Container from '../../Components/Container/Container';
import CustomModal from '../../Components/CustomModal/CustomModal';
import EventByYear from '../../Components/EventByYear/EventByYear';
import EventByLocation from '../../Components/EventByLocation/EventByLocation';
import GlobalFilterButton from '../../Components/GlobalFilterButton/GlobalFilterButton';
import { eluxTranslation } from '../../Translation/Translation';
import './Events.scss';

function Events() {
    const { eventDashboard } = eluxTranslation;
    return (
        <>
            <h2 className="section-title">{eventDashboard}</h2>
            <Container>
                <Row gutter={16}>
                    <Col span={12}>
                        <EventByYear />
                    </Col>
                    <Col span={12}>
                        <EventByLocation />
                    </Col>
                </Row>
            </Container>
            <GlobalFilterButton />
            <CustomModal />
        </>
    );
}

export default Events;
