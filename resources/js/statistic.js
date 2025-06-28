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
    let barChart;
    
    function getBarChartData(xAxis) {
        const features = ['usia', 'jumlah_anak', 'kelayakan_rumah', 'pendapatan'];
        
        if (xAxis === 'cluster') {
            // Default view - fitur pada sumbu x, cluster sebagai bars
            return {
                labels: features.map(f => fieldLabels[f]),
                datasets: barDatasets
            };
        } else {
            // Selected feature on x-axis, clusters as different bars
            // Reorganize data untuk menampilkan cluster pada sumbu x dan fitur sebagai bars
            // Kecuali fitur yang dipilih sebagai sumbu x
            
            const datasets = [];
            const featureIndex = features.indexOf(xAxis);
            
            if (featureIndex !== -1) {
                // Untuk setiap fitur kecuali yang dipilih sebagai sumbu x
                features.forEach((feature, idx) => {
                    if (feature !== xAxis) {
                        // Buat dataset baru untuk fitur ini
                        const data = [];
                        
                        // Ambil nilai fitur ini dari setiap cluster
                        for (let i = 0; i < barDatasets.length; i++) {
                            data.push(barDatasets[i].data[idx]);
                        }
                        
                        datasets.push({
                            label: fieldLabels[feature],
                            data: data,
                            backgroundColor: backgroundColors[datasets.length],
                            borderColor: borderColors[datasets.length],
                            borderWidth: 2
                        });
                    }
                });
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
                            text: xAxis === 'cluster' ? 'Fitur' : fieldLabels[xAxis]
                        }
                    },
                    y: { 
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: xAxis === 'cluster' ? 'Nilai' : 'Nilai Rata-rata'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, font: { size: 14 } }
                    },
                    title: {
                        display: true,
                        text: xAxis === 'cluster' ? 'Perbandingan Fitur per Cluster' : `Perbandingan Fitur berdasarkan ${fieldLabels[xAxis]}`,
                        font: { size: 16 }
                    }
                }
            }
        });
    }
    
    function updateBarChart() {
        const xAxis = document.getElementById('barXAxis').value;
        barChart.data = getBarChartData(xAxis);
        barChart.options.scales.x.title.text = xAxis === 'cluster' ? 'Fitur' : fieldLabels[xAxis];
        barChart.options.scales.y.title.text = xAxis === 'cluster' ? 'Nilai' : 'Nilai Rata-rata';
        barChart.options.plugins.title.text = xAxis === 'cluster' ? 'Perbandingan Fitur per Cluster' : `Perbandingan Fitur berdasarkan ${fieldLabels[xAxis]}`;
        barChart.update();
    }
    
    // Initialize bar chart
    initBarChart();
    
    // Add event listener for bar chart x-axis change
    document.getElementById('barXAxis').addEventListener('change', updateBarChart);

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
                    return {
                        x: Number(d[xField]),
                        y: Number(d[yField]),
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