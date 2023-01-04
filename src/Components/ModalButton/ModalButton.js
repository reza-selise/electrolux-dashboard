import { Button } from 'antd';
import React from 'react';
import { useDispatch } from 'react-redux';
import { setGraphURL } from '../../Redux/Slice/graphURL';
import { setLocation } from '../../Redux/Slice/locationSlice';
import { setModal } from '../../Redux/Slice/modalSlice';

function ModalButton({ location, children, graphID }) {
    // const { location, setLocation } = useState();

    const dispatch = useDispatch();
    const showModal = () => {
        dispatch(setModal());
        dispatch(setLocation(location));
        const graphCanvas = document.getElementById(graphID);
        if (graphCanvas !== null) {
            dispatch(setGraphURL(graphCanvas.toDataURL()));
        } else {
            dispatch(setGraphURL(''));
        }
    };

    return <Button onClick={showModal}>{children}</Button>;
}

export default ModalButton;
