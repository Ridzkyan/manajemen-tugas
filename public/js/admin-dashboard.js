// Pie Chart
const pieCtx = document.getElementById('pieChart');
new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: ['Dosen', 'Mahasiswa', 'Admin'],
        datasets: [{
            label: 'Data Users',
            data: [12, 80, 8],
            backgroundColor: ['#0077b6', '#e63946', '#f77f00'],
        }]
    }
});

// Bar Chart
const barCtx = document.getElementById('barChart');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: ['Statistika', 'Web Lanjut', 'Kalkulus', 'Jaringan', 'Diskrit'],
        datasets: [{
            label: 'Aktivitas',
            data: [35, 5, 10, 25, 12],
            backgroundColor: '#264653'
        }]
    }
});
