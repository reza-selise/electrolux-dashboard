import React, { useState } from 'react';
import './GraphTableSwitch.scss';

function GraphTableSwitch() {
    const [grapOrTable, setgGrapOrTable] = useState('graph');
    const handleSwitchChange = (e) => {
        setgGrapOrTable(e.target.value);
        console.log(e.target.value);
    };
    return (
        <form className="graph-table-switch" onChange={handleSwitchChange}>
            <label htmlFor="graph" className={grapOrTable === 'graph' ? 'graph-active' : ''}>
                <span>Graph</span>
                <input type="radio" id="graph" name="graphtableswitch" value="graph" />
            </label>

            <label htmlFor="table" className={grapOrTable === 'table' ? 'table-active' : ''}>
                <span>Table</span>
                <input type="radio" id="table" name="graphtableswitch" value="table" />
            </label>
        </form>
    );
}

export default GraphTableSwitch;
