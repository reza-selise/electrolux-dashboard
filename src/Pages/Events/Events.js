import React from 'react';
import { useGetEventByDateQuery } from '../../API/apiSlice';
import Container from '../../Components/Container/Container';
import './Events.scss';

function Events() {
    const { data, error, isLoading } = useGetEventByDateQuery('bulbasaur');
    return (
        <Container>
            <div className="events">
                {error ? (
                    <>Oh no, there was an error</>
                ) : isLoading ? (
                    <>Loading...</>
                ) : data ? (
                    <>
                        <h3>{data.species.name}</h3>
                        <img src={data.sprites.front_shiny} alt={data.species.name} />
                    </>
                ) : null}
            </div>
        </Container>
    );
}

export default Events;
