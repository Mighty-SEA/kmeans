// File ini berisi seluruh kode JavaScript dari statistic.blade.php
// Data dinamis dari Blade di-passing melalui window.statisticData

document.addEventListener('DOMContentLoaded', function() {
    if (!window.statisticData || !window.statisticData.clustered) return;

    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    const clusterLabels = window.statisticData.clusterLabels;
    const clusterData = window.statisticData.clusterData;
    const backgroundColors = window.statisticData.backgroundColors;
    const borderColors = window.statisticData.borderColors;

    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: clusterLabels,
            datasets: [{
                data: clusterData,
                backgroundColor: backgroundColors.slice(0, clusterLabels.length),
                borderColor: borderColors.slice(0, clusterLabels.length),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: { size: 14 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${value} data (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    const barDatasets = window.statisticData.barDatasets;
    const fieldLabels = window.statisticData.fieldLabels;
    const normalizedClusterMeans = window.statisticData.normalizedClusterMeans || [];
    let barChart;
    let isNormalized = false; // Status normalisasi
    
    function getBarChartData(xAxis) {
        const features = ['usia', 'jumlah_anak', 'kelayakan_rumah', 'pendapatan'];
        const normalizedFeatures = ['usia_normalized', 'jumlah_anak_normalized', 'kelayakan_rumah_normalized', 'pendapatan_normalized'];
        
        if (xAxis === 'cluster') {
            // Default view - fitur pada sumbu x, cluster sebagai bars
            const modifiedDatasets = [];
            
            for (let i = 0; i < barDatasets.length; i++) {
                const originalData = barDatasets[i].data;
                let newData;
                
                if (isNormalized && normalizedClusterMeans && normalizedClusterMeans[i]) {
                    // Gunakan data normalisasi dari database
                    newData = [
                        normalizedClusterMeans[i].usia,
                        normalizedClusterMeans[i].jumlah_anak,
                        normalizedClusterMeans[i].kelayakan_rumah,
                        normalizedClusterMeans[i].pendapatan
                    ];
                } else {
                    // Jika normalisasi tidak diaktifkan, gunakan skala yang sesuai
                    newData = [
                        originalData[0], // usia
                        originalData[1], // jumlah anak
                        originalData[2], // kelayakan rumah
                        originalData[3] / 1000 // pendapatan dibagi 1000 agar skalanya sesuai
                    ];
                }
                
                // Clone dataset asli
                const newDataset = {
                    label: barDatasets[i].label,
                    backgroundColor: barDatasets[i].backgroundColor,
                    borderColor: barDatasets[i].borderColor,
                    borderWidth: barDatasets[i].borderWidth,
                    data: newData
                };
                modifiedDatasets.push(newDataset);
            }
            
            const labels = features.map(f => {
                if (isNormalized) {
                    return fieldLabels[f + '_normalized'];
                } else if (f === 'pendapatan') {
                    return fieldLabels[f] + ' (÷1000)';
                } else {
                    return fieldLabels[f];
                }
            });
            
            return {
                labels: labels,
                datasets: modifiedDatasets
            };
        } else {
            // Selected feature on x-axis, clusters as different bars
            const datasets = [];
            const featureIndex = features.indexOf(xAxis);
            
            if (featureIndex !== -1) {
                let data;
                
                if (isNormalized && normalizedClusterMeans) {
                    // Gunakan data normalisasi dari database
                    data = normalizedClusterMeans.map(means => {
                        switch(xAxis) {
                            case 'usia': return means.usia;
                            case 'jumlah_anak': return means.jumlah_anak;
                            case 'kelayakan_rumah': return means.kelayakan_rumah;
                            case 'pendapatan': return means.pendapatan;
                            default: return 0;
                        }
                    });
                } else {
                    // Jika normalisasi tidak diaktifkan, gunakan nilai asli
                    data = barDatasets.map(dataset => dataset.data[featureIndex]);
                }
                
                datasets.push({
                    label: isNormalized ? fieldLabels[xAxis + '_normalized'] : fieldLabels[xAxis],
                    data: data,
                    backgroundColor: backgroundColors[0],
                    borderColor: borderColors[0],
                    borderWidth: 2
                });
                
                return {
                    labels: clusterLabels,
                    datasets: datasets
                };
            }
            
            return {
                labels: clusterLabels,
                datasets: datasets
            };
        }
    }
    
    function initBarChart() {
        const xAxis = document.getElementById('barXAxis').value;
        barChart = new Chart(barCtx, {
            type: 'bar',
            data: getBarChartData(xAxis),
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { 
                        grid: { display: false },
                        title: { 
                            display: true, 
                            text: xAxis === 'cluster' ? 'Fitur' : 'Cluster'
                        }
                    },
                    y: { 
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: getYAxisTitle(xAxis)
                        },
                        // Sesuaikan skala berdasarkan fitur yang dipilih
                        suggestedMax: getSuggestedMax(xAxis)
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, font: { size: 14 } }
                    },
                    title: {
                        display: true,
                        text: getChartTitle(xAxis),
                        font: { size: 16 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return getTooltipLabel(context, xAxis);
                            }
                        }
                    }
                }
            }
        });
    }
    
    function getYAxisTitle(xAxis) {
        if (xAxis === 'cluster') {
            return isNormalized ? 'Nilai Normalisasi' : 'Nilai';
        } else {
            return isNormalized ? fieldLabels[xAxis + '_normalized'] : fieldLabels[xAxis];
        }
    }
    
    function getChartTitle(xAxis) {
        if (xAxis === 'cluster') {
            return isNormalized ? 'Perbandingan Fitur per Cluster (Normalisasi)' : 'Perbandingan Fitur per Cluster';
        } else {
            return isNormalized ? `Perbandingan ${fieldLabels[xAxis + '_normalized']} per Cluster` : `Perbandingan ${fieldLabels[xAxis]} per Cluster`;
        }
    }
    
    function getTooltipLabel(context, xAxis) {
        // Jika mode cluster dan fitur adalah pendapatan, dan tidak dinormalisasi
        if (xAxis === 'cluster' && context.dataIndex === 3 && !isNormalized) {
            const value = context.raw * 1000;
            return `${context.dataset.label}: ${formatNumber(value)}`;
        }
        
        return `${context.dataset.label}: ${context.formattedValue}`;
    }
    
    function updateBarChart() {
        const xAxis = document.getElementById('barXAxis').value;
        barChart.data = getBarChartData(xAxis);
        barChart.options.scales.x.title.text = xAxis === 'cluster' ? 'Fitur' : 'Cluster';
        barChart.options.scales.y.title.text = getYAxisTitle(xAxis);
        barChart.options.plugins.title.text = getChartTitle(xAxis);
        
        // Update skala sumbu Y
        barChart.options.scales.y.suggestedMax = getSuggestedMax(xAxis);
        
        // Update tooltip callback
        barChart.options.plugins.tooltip.callbacks.label = function(context) {
            return getTooltipLabel(context, xAxis);
        };
        
        barChart.update();
    }
    
    // Fungsi untuk mendapatkan nilai maksimum yang disarankan untuk sumbu Y
    function getSuggestedMax(xAxis) {
        // Jika normalisasi diaktifkan, skala untuk data normalisasi
        if (isNormalized) {
            return 3; // Biasanya data normalisasi berada dalam rentang -3 hingga 3 (untuk z-score)
        }
        
        if (xAxis === 'cluster') {
            // Jika mode cluster, kita perlu mencari nilai maksimum dari semua fitur
            // Pendapatan sudah dibagi 1000, jadi kita bisa langsung membandingkan
            let maxValues = [];
            
            // Untuk setiap cluster
            for (let i = 0; i < barDatasets.length; i++) {
                const originalData = barDatasets[i].data;
                // Ambil nilai usia, jumlah anak, kelayakan rumah
                maxValues.push(originalData[0]); // usia
                maxValues.push(originalData[1]); // jumlah anak
                maxValues.push(originalData[2]); // kelayakan rumah
                maxValues.push(originalData[3] / 1000); // pendapatan dibagi 1000
            }
            
            // Kembalikan nilai maksimum + 20% untuk margin
            return Math.max(...maxValues) * 1.2;
        } else if (xAxis === 'pendapatan') {
            // Untuk pendapatan, biarkan Chart.js menentukan skala otomatis
            return undefined;
        } else {
            // Untuk fitur lainnya, cari nilai maksimum dari fitur tersebut di semua cluster
            const features = ['usia', 'jumlah_anak', 'kelayakan_rumah', 'pendapatan'];
            const featureIndex = features.indexOf(xAxis);
            
            if (featureIndex !== -1) {
                const values = barDatasets.map(dataset => dataset.data[featureIndex]);
                // Kembalikan nilai maksimum + 20% untuk margin
                return Math.max(...values) * 1.2;
            }
            
            return undefined;
        }
    }
    
    // Fungsi untuk memformat angka dengan pemisah ribuan
    function formatNumber(value) {
        return new Intl.NumberFormat().format(value);
    }
    
    // Initialize bar chart
    initBarChart();
    
    // Add event listener for bar chart x-axis change
    document.getElementById('barXAxis').addEventListener('change', updateBarChart);
    
    // Add event listener for normalization toggle
    document.getElementById('normalizeBarChart').addEventListener('change', function() {
        isNormalized = this.checked;
        updateBarChart();
    });

    // Scatter Chart
    const scatterData = window.statisticData.scatterData;
    const clusterCount = window.statisticData.clusterCount;

    function getDatasets(xField, yField) {
        const datasets = [];
        for (let i = 0; i < clusterCount; i++) {
            datasets.push({
                label: 'Cluster ' + (i+1),
                data: scatterData.filter(d => d.cluster === i).map(d => {
                    if (xField === 'silhouette' || yField === 'silhouette') {
                        if (d.silhouette === undefined) d.silhouette = 0;
                    }
                    
                    // Handle normalized fields
                    let xValue = d[xField];
                    let yValue = d[yField];
                    
                    // If field is normalized version, use that data
                    if (xField.endsWith('_normalized') && d[xField] === undefined) {
                        const baseField = xField.replace('_normalized', '');
                        xValue = d[baseField + '_normalized'];
                    }
                    
                    if (yField.endsWith('_normalized') && d[yField] === undefined) {
                        const baseField = yField.replace('_normalized', '');
                        yValue = d[baseField + '_normalized'];
                    }
                    
                    return {
                        x: Number(xValue),
                        y: Number(yValue),
                        nama: d.nama,
                        silhouette: d.silhouette
                    };
                }),
                backgroundColor: backgroundColors[i],
                borderColor: borderColors[i],
                borderWidth: 1,
                pointRadius: 6,
                pointHoverRadius: 8,
            });
        }
        return datasets;
    }

    let xField = document.getElementById('xAxis').value;
    let yField = document.getElementById('yAxis').value;
    const scatterCtx = document.getElementById('scatterChart').getContext('2d');
    let scatterChart = new Chart(scatterCtx, {
        type: 'scatter',
        data: { datasets: getDatasets(xField, yField) },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const d = context.raw;
                            let label = d.nama + ' (' + fieldLabels[xField] + ': ' + d.x + ', ' + fieldLabels[yField] + ': ' + d.y;
                            if (d.silhouette !== undefined && xField !== 'silhouette' && yField !== 'silhouette') {
                                label += ', Silhouette: ' + d.silhouette.toFixed(2);
                            }
                            return label + ')';
                        }
                    }
                }
            }
        }
    });

    document.getElementById('xAxis').addEventListener('change', updateScatterChart);
    document.getElementById('yAxis').addEventListener('change', updateScatterChart);

    function updateScatterChart() {
        xField = document.getElementById('xAxis').value;
        yField = document.getElementById('yAxis').value;
        scatterChart.data.datasets = getDatasets(xField, yField);
        scatterChart.options.scales = scatterChart.options.scales || {};
        scatterChart.options.scales.x = { title: { display: true, text: fieldLabels[xField] } };
        scatterChart.options.scales.y = { title: { display: true, text: fieldLabels[yField] } };
        scatterChart.update();
    }
}); 