import { Button } from 'antd';
import React from 'react';
import { useDispatch } from 'react-redux';
import { setLocation } from '../../Redux/Slice/locationSlice';
import { setModal } from '../../Redux/Slice/modalSlice';

function ModalButton({ location, children }) {
    // const { location, setLocation } = useState();
    const dispatch = useDispatch();
    const showModal = () => {
        dispatch(setModal());
        dispatch(setLocation(location));
    };

    return <Button onClick={showModal}>{children}</Button>;
}

export default ModalButton;
