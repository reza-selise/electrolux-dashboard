import React from 'react';
import './GraphTableSwitch.scss';

function GraphTableSwitch({ grapOrTable, setgGrapOrTable, name }) {
    const handleSwitchChange = e => {
        setgGrapOrTable(e.target.value);
    };
    return (
        <form className="graph-table-switch" onChange={handleSwitchChange}>
            <label
                htmlFor={`${name}graph`}
                className={grapOrTable === 'graph' ? 'graph-active' : ''}
            >
                <span>Graph</span>
                <input type="radio" id={`${name}graph`} name={name} value="graph" />
            </label>

            <label
                htmlFor={`${name}table`}
                className={grapOrTable === 'table' ? 'table-active' : ''}
            >
                <span>Table</span>
                <input type="radio" id={`${name}table`} name={name} value="table" />
            </label>
        </form>
    );
}

export default GraphTableSwitch;
