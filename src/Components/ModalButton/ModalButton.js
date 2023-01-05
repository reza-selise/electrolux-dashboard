import { Button } from 'antd';
import React from 'react';
import { useDispatch } from 'react-redux';
import { setGraphURL } from '../../Redux/Slice/graphURL';
import { setLocation } from '../../Redux/Slice/locationSlice';
import { setModal } from '../../Redux/Slice/modalSlice';
import { setTableURL } from '../../Redux/Slice/tableURL';

function ModalButton({ location, children, graphID, tableID }) {
    // const { location, setLocation } = useState();

    const dispatch = useDispatch();
    const showModal = () => {
        dispatch(setModal());
        dispatch(setLocation(location));
        const graphCanvas = document.getElementById(graphID);
        const tableCanvas = document.getElementById(tableID);
        if (graphCanvas !== null) {
            dispatch(setGraphURL(graphCanvas.toDataURL()));
        } else {
            dispatch(setGraphURL(''));
        }
        if (tableCanvas !== null) {
            dispatch(setTableURL(tableCanvas.innerHTML));
        } else {
            dispatch(setTableURL(''));
        }
    };

    return <Button onClick={showModal}>{children}</Button>;
}

export default ModalButton;
