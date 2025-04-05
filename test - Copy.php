<div id="apiData">
    <h3>Dynamic Data from API</h3>
    <div id="Welcome_Tist_Farmers"></div>
    <div id="Small_Groups"></div>
    <div id="Living_Trees_in_Millions"></div>
    <div id="Tonnes_in_Millions"></div>
    <div id="Trees_Alive"></div>
    <div id="Countries"></div>
    <div id="Footer_Date"></div>
    <div id="Tonnes_in_Numbers"></div>
    <div id="Living_Trees_in_Numbers"></div>
    <div id="Years_of_Success"></div>
    <div id="Metric_Tons_of_CO2"></div>
</div>

<script>
    async function fetchData() {
                const corsProxy = 'https://cors-anywhere.herokuapp.com/';

        const url = 'https://staging.program.tist.org/api/custom-config';
        const apiKey = 'cooHj9cphdfGb6iolNekdpB3cijmhambjff';
        const username = 'tistapiuser';
        const password = 'TistAPI@2024';
        const basicAuth = 'Basic ' + btoa(`${username}:${password}`);

        try {
            const response = await fetch(corsProxy + url, {
                method: 'GET',
                headers: {
                    'Authorization': basicAuth,
                    'X-API-KEY': apiKey,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) throw new Error("Network response was not ok.");
            const data = await response.json();

            // Display each data point in its corresponding HTML element
            document.getElementById("Welcome_Tist_Farmers").innerText = `Welcome Tist Farmers: ${data.Welcome_Tist_Farmers}`;
            document.getElementById("Small_Groups").innerText = `Small Groups: ${data.Small_Groups}`;
            document.getElementById("Living_Trees_in_Millions").innerText = `Living Trees in Millions: ${data.Living_Trees_in_Millions}`;
            document.getElementById("Tonnes_in_Millions").innerText = `Tonnes in Millions: ${data.Tonnes_in_Millions}`;
            document.getElementById("Trees_Alive").innerText = `Trees Alive: ${data.Trees_Alive}`;
            document.getElementById("Countries").innerText = `Countries: ${data.Countries}`;
            document.getElementById("Footer_Date").innerText = `Footer Date: ${data.Footer_Date}`;
            document.getElementById("Tonnes_in_Numbers").innerText = `Tonnes in Numbers: ${data.Tonnes_in_Numbers}`;
            document.getElementById("Living_Trees_in_Numbers").innerText = `Living Trees in Numbers: ${data.Living_Trees_in_Numbers}`;
            document.getElementById("Years_of_Success").innerText = `Years of Success: ${data.Years_of_Success}`;
            document.getElementById("Metric_Tons_of_CO2").innerText = `Metric Tons of CO2: ${data.Metric_Tons_of_CO2}`;

        } catch (error) {
            document.getElementById("apiData").innerText = `Error: ${error.message}`;
        }
    }

    fetchData();
</script>
