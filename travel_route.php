<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Travel Map</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            text-align: center;
            padding: 30px 0;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .subtitle {
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-bottom: 30px;
        }

        .map-container {
            position: relative;
            width: 50%;
            max-width: 1000px;
            height: 600px;
            margin: 0 auto;
            background-image: url('assets/images/cc.jpg');
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .map-point {
            position: absolute;
            width: 30px;
            height: 30px;
            background-color: #e74c3c;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, background-color 0.3s ease;
            z-index: 10;
        }

        .map-point::after {
            content: '';
            position: absolute;
            width: 50px;
            height: 50px;
            background-color: rgba(231, 76, 60, 0.3);
            border-radius: 50%;
            z-index: -1;
            animation: pulse 2s infinite;
        }

        .map-point:hover {
            transform: scale(1.2);
            background-color: #c0392b;
        }

        .map-point.point1 {
            top: 30%;
            left: 20%;
        }

        .map-point.point2 {
            top: 60%;
            left: 40%;
        }

        .map-point.point3 {
            top: 25%;
            left: 70%;
        }

        .map-point.point4 {
            top: 70%;
            left: 75%;
        }

        .info-box {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(44, 62, 80, 0.9);
            color: white;
            padding: 20px;
            transform: translateY(100%);
            transition: transform 0.5s ease;
        }

        .map-container:hover .info-box {
            transform: translateY(0);
        }

        .admin-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 20px;
            gap: 10px;
            /* ระยะห่างระหว่างปุ่ม */
        }



        /* .path {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .path svg {
            width: 100%;
            height: 100%;
        }

        .path path {
            fill: none;
            stroke: #3498db;
            stroke-width: 3;
            stroke-dasharray: 8;
            stroke-linecap: round;
            animation: dash 30s linear infinite;
        } */

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }

            70% {
                transform: scale(1.5);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 0;
            }
        }

        @keyframes dash {
            to {
                stroke-dashoffset: 1000;
            }
        }

        footer {
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
            color: #7f8c8d;
        }

        @media (max-width: 768px) {
            .map-container {
                height: 400px;
            }

            h1 {
                font-size: 2rem;
            }
        }
    </style>
    <!-- /* แก้ไข map และลากจุด */ -->
    <style>
        .map-editor {
            position: relative;
            width: 100%;
            height: 600px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border: 1px solid #ccc;
        }

        .map-editor.edit-mode {
            cursor: pointer;
            border: 2px dashed #ff6600;
            background-color: rgba(255, 255, 255, 0.9);
        }

        /* สไตล์สำหรับจุดที่สามารถแก้ไขได้ */
        .point-editor.editable {
            cursor: move;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .point-editor.editable:hover {
            transform: scale(1.1);
            box-shadow: 0 0 10px rgba(255, 102, 0, 0.7);
        }

        /* สไตล์สำหรับจุดที่ถูกเลือก */
        .point-editor.selected {
            border: 3px solid #ff6600;
            transform: scale(1.2);
            box-shadow: 0 0 15px rgba(255, 102, 0, 0.8);
            z-index: 100;
        }

        /* สไตล์สำหรับจุดควบคุมของเส้นทาง */
        .control-point {
            cursor: move;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .edit-mode .control-point {
            opacity: 1;
        }

        /* สไตล์สำหรับแผงข้อมูลจุด */
        .point-info-panel {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .point-info-content {
            display: flex;
            margin-top: 10px;
        }

        .point-image-container {
            width: 120px;
            text-align: center;
            margin-right: 20px;
        }

        .point-image-container img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
            margin-bottom: 10px;
        }

        .point-details {
            flex: 1;
        }

        /* สไตล์สำหรับปุ่ม */
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .btn.active {
            background-color: #ff6600;
        }

        .btn.active:hover {
            background-color: #e55c00;
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
        }

        /* สไตล์สำหรับการแจ้งเตือน */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            display: none;
        }

        .notification.error {
            background-color: #f44336;
        }

        /* สไตล์สำหรับฟอร์ม */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
    <!-- /* แก้ไข map และลากจุด */ -->
</head>

<body>
    <div class="container">
        <header>
            <h1>เส้นทางการเดินทาง</h1>
            <p class="subtitle">คลิกที่จุดเพื่อเยี่ยมชมแต่ละสถานที่</p>
        </header>

        <div class="map-container">
            <!-- SVG Paths connecting points -->
            <!-- <div class="path">
                <svg>
                    <path d="M205,180 C250,250 350,300 400,360" />
                    <path d="M400,360 C450,400 600,200 700,150" />
                    <path d="M700,150 C750,200 700,350 750,420" />
                    <path d="M750,420 C600,450 300,350 205,180" />
                </svg>
            </div> -->

            <!-- Map Points -->
            <a href="https://3dvistatraining.com" target="_blank" class="map-point point1">1</a>
            <a href="https://3dvistatraining.com" target="_blank" class="map-point point2">2</a>
            <a href="https://3dvistatraining.com" target="_blank" class="map-point point3">3</a>
            <a href="https://3dvistatraining.com" target="_blank" class="map-point point4">4</a>

            <div class="info-box">
                <h3>เกี่ยวกับเส้นทาง</h3>
                <p>นี่คือแผนที่แสดงจุดสำคัญ 4 จุดในเส้นทางการเดินทาง คลิกที่แต่ละจุดเพื่อเยี่ยมชมรายละเอียดเพิ่มเติม</p>
            </div>
        </div>
        <div>
            <!-- เพิ่มปุ่มแก้ไขลงในส่วนของ admin panel -->
            <div class="admin-controls">
                <button id="editButton" class="btn">แก้ไขแผนที่</button>
                <button id="saveChanges" class="btn">บันทึกการเปลี่ยนแปลง</button>
            </div>

            <!-- เพิ่มส่วนแสดงข้อมูลจุดที่เลือก -->
            <div id="pointInfo" class="point-info-panel" style="display: none;">
                <h3>ข้อมูลจุด</h3>
                <div class="point-info-content">
                    <div class="point-image-container">
                        <img id="pointInfoImage" src="images/point-marker.png" alt="Point Image">
                        <button id="uploadImageButton" class="btn btn-sm">เปลี่ยนรูปภาพ</button>
                        <input type="file" id="imageUploadInput" accept="image/*" style="display: none;">
                    </div>
                    <div class="point-details">
                        <h4 id="pointInfoLabel">จุดที่ 1</h4>
                        <p id="pointInfoCoords">ตำแหน่ง: 0%, 0%</p>
                        <div class="form-group">
                            <label>ป้ายกำกับ:</label>
                            <input type="text" class="point-label-input" data-point-number="1">
                        </div>
                    </div>
                </div>
                <!-- เพิ่ม dropdown สำหรับเลือกจุด (ตัวเลือก) -->
                <div class="form-group">
                    <label for="pointSelector">เลือกจุด:</label>
                    <select id="pointSelector">
                        <option value="">-- เลือกจุด --</option>
                        <?php foreach ($mapData['points'] as $point) : ?>
                            <option value="<?php echo $point['point_number']; ?>">
                                <?php echo $point['label'] ? $point['label'] : 'จุดที่ ' . $point['point_number']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <footer>
                <p>&copy; 2025 เส้นทางการเดินทาง</p>
            </footer>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Define missing variables
                const mapEditor = document.querySelector('.map-container');
                const editorPoints = [{
                        data: {
                            point_number: '1',
                            x_position: 20,
                            y_position: 30,
                            label: 'จุดที่ 1'
                        }
                    },
                    {
                        data: {
                            point_number: '2',
                            x_position: 40,
                            y_position: 60,
                            label: 'จุดที่ 2'
                        }
                    },
                    {
                        data: {
                            point_number: '3',
                            x_position: 70,
                            y_position: 25,
                            label: 'จุดที่ 3'
                        }
                    },
                    {
                        data: {
                            point_number: '4',
                            x_position: 75,
                            y_position: 70,
                            label: 'จุดที่ 4'
                        }
                    }
                ];

                // Add missing showNotification function
                function showNotification(message, isError = false) {
                    // Create notification if it doesn't exist
                    let notification = document.querySelector('.notification');
                    if (!notification) {
                        notification = document.createElement('div');
                        notification.className = 'notification';
                        document.body.appendChild(notification);
                    }

                    // Set message and display
                    notification.textContent = message;
                    notification.classList.toggle('error', isError);
                    notification.style.display = 'block';

                    // Hide after 3 seconds
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 3000);
                }

                // Initialize variables
                let editMode = false;
                let selectedPoint = null;

                // Find DOM elements
                const editButton = document.getElementById('editButton');
                const saveChanges = document.getElementById('saveChanges');
                const pointSelector = document.getElementById('pointSelector');
                const uploadImageButton = document.getElementById('uploadImageButton');
                const imageUploadInput = document.getElementById('imageUploadInput');
                const mapPoints = document.querySelectorAll('.map-point');

                // Add point-editor class to all map points for consistent selection
                mapPoints.forEach(point => {
                    point.classList.add('point-editor');
                    // Extract point number from class name (e.g., point1 -> 1)
                    const pointNumber = point.classList[1].replace('point', '');
                    point.setAttribute('data-point-number', pointNumber);

                    // Initialize draggable functionality for all points
                    makeElementDraggable(point);
                });

                // Add event listener for edit button
                if (editButton) {
                    editButton.addEventListener('click', function() {
                        editMode = !editMode;

                        // Toggle button text and class
                        this.textContent = editMode ? 'ยกเลิกการแก้ไข' : 'แก้ไขแผนที่';
                        this.classList.toggle('active');

                        // Toggle map editor class
                        mapEditor.classList.toggle('edit-mode');

                        if (editMode) {
                            // Enable dragging and editing
                            enableDragging();
                            showNotification('โหมดแก้ไขเปิดทำงาน กดที่จุดเพื่อเลือกและย้าย');
                        } else {
                            // Disable dragging and editing
                            disableDragging();
                            clearSelection();
                            showNotification('ออกจากโหมดแก้ไขแล้ว');
                        }
                    });
                }

                // Add event listener for save button
                if (saveChanges) {
                    saveChanges.addEventListener('click', function() {
                        // Here you would typically save the changes to the server
                        // For now, just show a notification
                        showNotification('บันทึกการเปลี่ยนแปลงเรียบร้อยแล้ว');

                        // Update the position styles for each point
                        editorPoints.forEach(point => {
                            const pointElement = document.querySelector(`.map-point.point${point.data.point_number}`);
                            if (pointElement) {
                                pointElement.style.left = `${point.data.x_position}%`;
                                pointElement.style.top = `${point.data.y_position}%`;
                            }
                        });
                    });
                }

                // Function to enable dragging for all points
                function enableDragging() {
                    mapPoints.forEach(point => {
                        point.addEventListener('click', selectPoint);
                        point.classList.add('editable');
                    });
                }

                // Function to disable dragging for all points
                function disableDragging() {
                    mapPoints.forEach(point => {
                        point.removeEventListener('click', selectPoint);
                        point.classList.remove('editable');
                    });
                }

                // Function to select a point
                function selectPoint(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Deselect previously selected point
                    if (selectedPoint) {
                        selectedPoint.classList.remove('selected');
                    }

                    // Select current point
                    this.classList.add('selected');
                    selectedPoint = this;

                    // Update point selector dropdown
                    if (pointSelector) {
                        pointSelector.value = this.dataset.pointNumber;
                    }

                    // Show point info
                    showPointInfo(this.dataset.pointNumber);
                }

                // Function to clear point selection
                function clearSelection() {
                    if (selectedPoint) {
                        selectedPoint.classList.remove('selected');
                        selectedPoint = null;
                    }

                    // Hide point info
                    hidePointInfo();
                }

                // Function to show point info
                function showPointInfo(pointNumber) {
                    // Find point data
                    const pointData = editorPoints.find(p => p.data.point_number === pointNumber);

                    if (pointData && document.getElementById('pointInfo')) {
                        // Show point info div
                        const pointInfo = document.getElementById('pointInfo');
                        pointInfo.style.display = 'block';

                        // Update point info content
                        document.getElementById('pointInfoLabel').textContent = pointData.data.label || 'จุดที่ ' + pointNumber;
                        document.getElementById('pointInfoCoords').textContent = `ตำแหน่ง: ${pointData.data.x_position.toFixed(2)}%, ${pointData.data.y_position.toFixed(2)}%`;

                        // Update point image
                        const pointImage = document.getElementById('pointInfoImage');
                        if (pointImage) {
                            pointImage.src = pointData.data.point_image || 'images/point-marker.png';
                        }

                        // Update label input
                        const labelInput = document.querySelector(`.point-label-input[data-point-number="${pointNumber}"]`);
                        if (labelInput) {
                            labelInput.value = pointData.data.label || '';
                        }
                    }
                }

                // Function to hide point info
                function hidePointInfo() {
                    const pointInfo = document.getElementById('pointInfo');
                    if (pointInfo) {
                        pointInfo.style.display = 'none';
                    }
                }

                // Add event listener for map container
                mapEditor.addEventListener('click', function(e) {
                    // Clear selection when clicking on the background
                    if (e.target === this && editMode) {
                        clearSelection();
                    }
                });

                // Make elements draggable
                function makeElementDraggable(element) {
                    let isDragging = false;
                    let offsetX, offsetY;

                    element.addEventListener('mousedown', startDrag);
                    element.addEventListener('touchstart', startDrag);

                    function startDrag(e) {
                        // Only allow dragging in edit mode
                        if (!editMode) return;

                        e.preventDefault();
                        isDragging = true;

                        // Select this point
                        if (element.classList.contains('point-editor')) {
                            clearSelection();
                            element.classList.add('selected');
                            selectedPoint = element;
                            showPointInfo(element.dataset.pointNumber);
                        }

                        // Calculate initial offset
                        const rect = element.getBoundingClientRect();

                        if (e.type === 'touchstart') {
                            offsetX = e.touches[0].clientX - rect.left;
                            offsetY = e.touches[0].clientY - rect.top;
                        } else {
                            offsetX = e.clientX - rect.left;
                            offsetY = e.clientY - rect.top;
                        }

                        document.addEventListener('mousemove', drag);
                        document.addEventListener('touchmove', drag);
                        document.addEventListener('mouseup', endDrag);
                        document.addEventListener('touchend', endDrag);
                    }

                    function drag(e) {
                        if (!isDragging) return;
                        e.preventDefault();

                        let x, y;
                        const container = mapEditor.getBoundingClientRect();

                        if (e.type === 'touchmove') {
                            x = e.touches[0].clientX - container.left - offsetX;
                            y = e.touches[0].clientY - container.top - offsetY;
                        } else {
                            x = e.clientX - container.left - offsetX;
                            y = e.clientY - container.top - offsetY;
                        }

                        // Convert to percentage
                        const percentX = (x / container.width) * 100;
                        const percentY = (y / container.height) * 100;

                        // Constrain to boundaries
                        const boundedX = Math.max(0, Math.min(100, percentX));
                        const boundedY = Math.max(0, Math.min(100, percentY));

                        // Update element position
                        element.style.left = `${boundedX}%`;
                        element.style.top = `${boundedY}%`;

                        // Update data
                        const pointNumber = element.dataset.pointNumber;
                        const pointData = editorPoints.find(p => p.data.point_number === pointNumber);
                        if (pointData) {
                            pointData.data.x_position = boundedX;
                            pointData.data.y_position = boundedY;

                            // Update displayed coordinates if this is the selected point
                            if (selectedPoint === element) {
                                const coordsInfo = document.getElementById('pointInfoCoords');
                                if (coordsInfo) {
                                    coordsInfo.textContent = `ตำแหน่ง: ${boundedX.toFixed(2)}%, ${boundedY.toFixed(2)}%`;
                                }
                            }
                        }
                    }

                    function endDrag() {
                        isDragging = false;
                        document.removeEventListener('mousemove', drag);
                        document.removeEventListener('touchmove', drag);
                        document.removeEventListener('mouseup', endDrag);
                        document.removeEventListener('touchend', endDrag);
                    }
                }

                // Handle image upload button
                if (uploadImageButton && imageUploadInput) {
                    uploadImageButton.addEventListener('click', function() {
                        // Check if a point is selected
                        if (!selectedPoint) {
                            showNotification('กรุณาเลือกจุดก่อนอัปโหลดรูปภาพ', true);
                            return;
                        }

                        // Trigger file selection dialog
                        imageUploadInput.click();
                    });

                    // Handle file selection
                    imageUploadInput.addEventListener('change', function() {
                        if (!selectedPoint || !this.files || !this.files[0]) {
                            return;
                        }

                        const pointNumber = selectedPoint.dataset.pointNumber;
                        const file = this.files[0];

                        // Check file type
                        if (!file.type.match('image.*')) {
                            showNotification('กรุณาเลือกไฟล์รูปภาพเท่านั้น', true);
                            return;
                        }

                        // Create FormData for upload (commented out as we don't have a server to handle this)
                        /*
                        const formData = new FormData();
                        formData.append('point_image', file);
                        formData.append('point_number', pointNumber);
                        
                        // Send image to server
                        fetch('upload-point-image.php', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => response.json())
                        .then(result => {
                            // Handle success
                        })
                        .catch(error => {
                            showNotification(`เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ: ${error.message}`, true);
                        });
                        */

                        // For demonstration, update with a local preview
                        const imageUrl = URL.createObjectURL(file);
                        selectedPoint.style.backgroundImage = `url('${imageUrl}')`;
                        selectedPoint.style.backgroundColor = 'transparent';

                        // Update pointData
                        const pointData = editorPoints.find(p => p.data.point_number === pointNumber);
                        if (pointData) {
                            pointData.data.point_image = imageUrl;
                        }

                        // Update image in point info panel
                        const pointInfoImage = document.getElementById('pointInfoImage');
                        if (pointInfoImage) {
                            pointInfoImage.src = imageUrl;
                        }

                        showNotification(`แสดงตัวอย่างรูปภาพสำหรับจุดที่ ${pointNumber}`);

                        // Reset input to allow selecting the same file again
                        this.value = '';
                    });
                }

                // Handle point selector dropdown
                if (pointSelector) {
                    pointSelector.addEventListener('change', function() {
                        const pointNumber = this.value;
                        if (pointNumber) {
                            // Find the corresponding point element
                            const pointElement = document.querySelector(`.point-editor[data-point-number="${pointNumber}"]`);
                            if (pointElement) {
                                // Simulate clicking the point
                                clearSelection();
                                pointElement.classList.add('selected');
                                selectedPoint = pointElement;
                                showPointInfo(pointNumber);
                            }
                        } else {
                            clearSelection();
                        }
                    });
                }
            });
            // JavaScript can be added here for additional functionality
            document.addEventListener('DOMContentLoaded', function() {
                // You can add more interactive features here if needed
                console.log('Map loaded successfully');
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // เพิ่มตัวแปรสำหรับโหมดการแก้ไข
                let editMode = false;

                // ค้นหา DOM elements สำหรับการแก้ไข
                const editButton = document.getElementById('editButton');
                const pointSelector = document.getElementById('pointSelector');
                const uploadImageButton = document.getElementById('uploadImageButton');
                const imageUploadInput = document.getElementById('imageUploadInput');

                // เพิ่ม event listener สำหรับปุ่มแก้ไข
                if (editButton) {
                    editButton.addEventListener('click', function() {
                        editMode = !editMode;

                        // เปลี่ยนข้อความและสถานะของปุ่ม
                        this.textContent = editMode ? 'ยกเลิกการแก้ไข' : 'แก้ไขแผนที่';
                        this.classList.toggle('active');

                        // เปลี่ยนสถานะ cursor และ class ของแผนที่
                        mapEditor.classList.toggle('edit-mode');

                        if (editMode) {
                            // เปิดใช้งานการลาก/การแก้ไขจุด
                            enableDragging();
                            showNotification('โหมดแก้ไขเปิดทำงาน กดที่จุดเพื่อเลือกและย้าย');
                        } else {
                            // ปิดการใช้งานการลาก/การแก้ไขจุด
                            disableDragging();
                            clearSelection();
                            showNotification('ออกจากโหมดแก้ไขแล้ว');
                        }
                    });
                }

                // ตัวแปรสำหรับเก็บจุดที่ถูกเลือก
                let selectedPoint = null;

                // ฟังก์ชันสำหรับการทำให้โหมดแก้ไขทำงาน
                function enableDragging() {
                    // เพิ่ม event listeners สำหรับทุกจุด
                    document.querySelectorAll('.point-editor').forEach(point => {
                        point.addEventListener('click', selectPoint);
                        point.classList.add('editable');
                    });
                }

                // ฟังก์ชันสำหรับปิดโหมดแก้ไข
                function disableDragging() {
                    document.querySelectorAll('.point-editor').forEach(point => {
                        point.removeEventListener('click', selectPoint);
                        point.classList.remove('editable');
                    });
                }

                // ฟังก์ชันสำหรับเลือกจุด
                function selectPoint(e) {
                    e.stopPropagation();

                    // ยกเลิกการเลือกจุดที่เคยเลือกไว้ก่อนหน้า
                    if (selectedPoint) {
                        selectedPoint.classList.remove('selected');
                    }

                    // เลือกจุดปัจจุบัน
                    this.classList.add('selected');
                    selectedPoint = this;

                    // อัปเดตตัวเลือกจุดใน dropdown (ถ้ามี)
                    if (pointSelector) {
                        pointSelector.value = this.dataset.pointNumber;
                    }

                    // แสดงข้อมูลจุดที่เลือก
                    showPointInfo(this.dataset.pointNumber);
                }

                // ฟังก์ชันเพื่อยกเลิกการเลือกจุด
                function clearSelection() {
                    if (selectedPoint) {
                        selectedPoint.classList.remove('selected');
                        selectedPoint = null;
                    }

                    // ซ่อนข้อมูลจุด
                    hidePointInfo();
                }

                // เพิ่ม event listener สำหรับการคลิกที่พื้นหลังเพื่อยกเลิกการเลือก
                mapEditor.addEventListener('click', function(e) {
                    // ยกเลิกการเลือกเฉพาะเมื่อคลิกบนพื้นหลัง ไม่ใช่บนจุด
                    if (e.target === this && editMode) {
                        clearSelection();
                    }
                });

                // ฟังก์ชันเพื่อแสดงข้อมูลของจุดที่เลือก
                function showPointInfo(pointNumber) {
                    // ค้นหาข้อมูลจุด
                    const pointData = editorPoints.find(p => p.data.point_number === pointNumber);

                    if (pointData && document.getElementById('pointInfo')) {
                        // แสดงข้อมูลจุดใน div pointInfo
                        const pointInfo = document.getElementById('pointInfo');
                        pointInfo.style.display = 'block';

                        // อัปเดตเนื้อหาข้อมูลจุด
                        document.getElementById('pointInfoLabel').textContent = pointData.data.label || 'จุดที่ ' + pointNumber;
                        document.getElementById('pointInfoCoords').textContent = `ตำแหน่ง: ${pointData.data.x_position.toFixed(2)}%, ${pointData.data.y_position.toFixed(2)}%`;

                        // แสดงรูปภาพปัจจุบันของจุด (ถ้ามี)
                        const pointImage = document.getElementById('pointInfoImage');
                        if (pointImage) {
                            pointImage.src = pointData.data.point_image || 'images/point-marker.png';
                        }

                        // อัปเดต input สำหรับแก้ไขป้ายกำกับ (ถ้ามี)
                        const labelInput = document.querySelector(`.point-label-input[data-point-number="${pointNumber}"]`);
                        if (labelInput) {
                            labelInput.value = pointData.data.label || '';
                        }
                    }
                }

                // ฟังก์ชันเพื่อซ่อนข้อมูลจุด
                function hidePointInfo() {
                    const pointInfo = document.getElementById('pointInfo');
                    if (pointInfo) {
                        pointInfo.style.display = 'none';
                    }
                }

                // เพิ่ม event listener สำหรับปุ่มอัปโหลดรูปภาพ
                if (uploadImageButton && imageUploadInput) {
                    uploadImageButton.addEventListener('click', function() {
                        // ตรวจสอบว่ามีการเลือกจุดหรือไม่
                        if (!selectedPoint) {
                            showNotification('กรุณาเลือกจุดก่อนอัปโหลดรูปภาพ', true);
                            return;
                        }

                        // กระตุ้น dialog การเลือกไฟล์
                        imageUploadInput.click();
                    });

                    // จัดการการเปลี่ยนแปลงของ input ไฟล์
                    imageUploadInput.addEventListener('change', function() {
                        if (!selectedPoint || !this.files || !this.files[0]) {
                            return;
                        }

                        const pointNumber = selectedPoint.dataset.pointNumber;
                        const file = this.files[0];

                        // ตรวจสอบชนิดไฟล์ (อนุญาตเฉพาะรูปภาพ)
                        if (!file.type.match('image.*')) {
                            showNotification('กรุณาเลือกไฟล์รูปภาพเท่านั้น', true);
                            return;
                        }

                        // สร้าง FormData สำหรับการอัปโหลด
                        const formData = new FormData();
                        formData.append('point_image', file);
                        formData.append('point_number', pointNumber);

                        // ส่งรูปภาพไปยังเซิร์ฟเวอร์
                        fetch('upload-point-image.php', {
                                method: 'POST',
                                body: formData,
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    showNotification(`อัปโหลดรูปภาพสำหรับจุดที่ ${pointNumber} สำเร็จ`);

                                    // อัปเดตรูปภาพของจุดในแผนที่
                                    selectedPoint.style.backgroundImage = `url('${result.image_url}')`;
                                    selectedPoint.style.backgroundColor = 'transparent';
                                    selectedPoint.textContent = '';

                                    // อัปเดตภาพในส่วนข้อมูลจุด
                                    const pointInfoImage = document.getElementById('pointInfoImage');
                                    if (pointInfoImage) {
                                        pointInfoImage.src = result.image_url;
                                    }

                                    // อัปเดตข้อมูลในโครงสร้างข้อมูล
                                    const pointData = editorPoints.find(p => p.data.point_number === pointNumber);
                                    if (pointData) {
                                        pointData.data.point_image = result.image_url;
                                    }
                                } else {
                                    showNotification(`เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ: ${result.message}`, true);
                                }
                            })
                            .catch(error => {
                                showNotification(`เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ: ${error.message}`, true);
                            });

                        // รีเซ็ต input เพื่อให้สามารถเลือกไฟล์เดิมซ้ำได้
                        this.value = '';
                    });
                }

                // เพิ่ม event listener สำหรับ dropdown เลือกจุด (ถ้ามี)
                if (pointSelector) {
                    pointSelector.addEventListener('change', function() {
                        const pointNumber = this.value;
                        if (pointNumber) {
                            // หาองค์ประกอบจุดที่เกี่ยวข้อง
                            const pointElement = document.querySelector(`.point-editor[data-point-number="${pointNumber}"]`);
                            if (pointElement) {
                                // จำลองการคลิกที่จุด
                                clearSelection();
                                pointElement.classList.add('selected');
                                selectedPoint = pointElement;
                                showPointInfo(pointNumber);
                            }
                        } else {
                            clearSelection();
                        }
                    });
                }

                // ปรับปรุงฟังก์ชัน makeElementDraggable ให้ทำงานเฉพาะเมื่ออยู่ในโหมดแก้ไข
                function makeElementDraggable(element) {
                    let isDragging = false;
                    let offsetX, offsetY;

                    element.addEventListener('mousedown', startDrag);
                    element.addEventListener('touchstart', startDrag);

                    function startDrag(e) {
                        // ลากได้เฉพาะเมื่ออยู่ในโหมดแก้ไข
                        if (!editMode) return;

                        // ยกเลิกการเลือกข้อความและป้องกันการเรียกใช้เบราว์เซอร์เริ่มต้น
                        e.preventDefault();
                        isDragging = true;

                        // เลือกจุดนี้
                        if (element.classList.contains('point-editor')) {
                            clearSelection();
                            element.classList.add('selected');
                            selectedPoint = element;
                            showPointInfo(element.dataset.pointNumber);
                        }

                        // คำนวณ offset เริ่มต้น
                        const rect = element.getBoundingClientRect();

                        if (e.type === 'touchstart') {
                            offsetX = e.touches[0].clientX - rect.left;
                            offsetY = e.touches[0].clientY - rect.top;
                        } else {
                            offsetX = e.clientX - rect.left;
                            offsetY = e.clientY - rect.top;
                        }

                        document.addEventListener('mousemove', drag);
                        document.addEventListener('touchmove', drag);
                        document.addEventListener('mouseup', endDrag);
                        document.addEventListener('touchend', endDrag);
                    }

                    function drag(e) {
                        if (!isDragging) return;
                        e.preventDefault();

                        let x, y;
                        const container = mapEditor.getBoundingClientRect();

                        if (e.type === 'touchmove') {
                            x = e.touches[0].clientX - container.left - offsetX;
                            y = e.touches[0].clientY - container.top - offsetY;
                        } else {
                            x = e.clientX - container.left - offsetX;
                            y = e.clientY - container.top - offsetY;
                        }

                        // แปลงเป็นเปอร์เซ็นต์
                        const percentX = (x / container.width) * 100;
                        const percentY = (y / container.height) * 100;

                        // จำกัดให้อยู่ภายในขอบเขต
                        const boundedX = Math.max(0, Math.min(100, percentX));
                        const boundedY = Math.max(0, Math.min(100, percentY));

                        // อัปเดตตำแหน่งองค์ประกอบ
                        element.style.left = `${boundedX}%`;
                        element.style.top = `${boundedY}%`;

                        // อัปเดตข้อมูล
                        const pointNumber = element.dataset.pointNumber;
                        const pointData = editorPoints.find(p => p.data.point_number === pointNumber);
                        if (pointData) {
                            pointData.data.x_position = boundedX;
                            pointData.data.y_position = boundedY;

                            // อัปเดตข้อมูลที่แสดงถ้าเป็นจุดที่ถูกเลือก
                            if (selectedPoint === element) {
                                const coordsInfo = document.getElementById('pointInfoCoords');
                                if (coordsInfo) {
                                    coordsInfo.textContent = `ตำแหน่ง: ${boundedX.toFixed(2)}%, ${boundedY.toFixed(2)}%`;
                                }
                            }
                        }
                    }

                    function endDrag() {
                        isDragging = false;
                        document.removeEventListener('mousemove', drag);
                        document.removeEventListener('touchmove', drag);
                        document.removeEventListener('mouseup', endDrag);
                        document.removeEventListener('touchend', endDrag);
                    }
                }

                // ปรับปรุงฟังก์ชัน makeDraggable สำหรับควบคุมจุดของเส้นทาง
                function makeDraggable(element, updateCallback) {
                    let selected = false;
                    let offset = {
                        x: 0,
                        y: 0
                    };

                    element.addEventListener('mousedown', startDrag);
                    element.addEventListener('touchstart', startDrag);

                    function startDrag(event) {
                        // ลากได้เฉพาะเมื่ออยู่ในโหมดแก้ไข
                        if (!editMode) return;

                        event.preventDefault();
                        selected = true;

                        if (event.type === 'touchstart') {
                            offset.x = event.touches[0].clientX - parseFloat(element.getAttribute('cx'));
                            offset.y = event.touches[0].clientY - parseFloat(element.getAttribute('cy'));
                        } else {
                            offset.x = event.clientX - parseFloat(element.getAttribute('cx'));
                            offset.y = event.clientY - parseFloat(element.getAttribute('cy'));
                        }

                        document.addEventListener('mousemove', drag);
                        document.addEventListener('touchmove', drag);
                        document.addEventListener('mouseup', endDrag);
                        document.addEventListener('touchend', endDrag);
                    }

                    function drag(event) {
                        if (selected) {
                            event.preventDefault();

                            let x, y;
                            if (event.type === 'touchmove') {
                                x = event.touches[0].clientX - offset.x;
                                y = event.touches[0].clientY - offset.y;
                            } else {
                                x = event.clientX - offset.x;
                                y = event.clientY - offset.y;
                            }

                            element.setAttribute('cx', x);
                            element.setAttribute('cy', y);

                            if (updateCallback) updateCallback();
                        }
                    }

                    function endDrag() {
                        selected = false;
                        document.removeEventListener('mousemove', drag);
                        document.removeEventListener('touchmove', drag);
                        document.removeEventListener('mouseup', endDrag);
                        document.removeEventListener('touchend', endDrag);
                    }
                }
            });
        </script>
<!-- แผนที่อัปเดต -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    let editMode = false;
    let selectedPoint = null;
    const mapEditor = document.querySelector('.map-container');
    const editButton = document.getElementById('editButton');
    const saveChanges = document.getElementById('saveChanges');
    const mapPoints = document.querySelectorAll('.map-point');

    // โหลดตำแหน่งของจุดจาก LocalStorage ถ้ามี
    mapPoints.forEach(point => {
        const pointNumber = point.classList[1]; // เช่น "point1"
        const savedPosition = localStorage.getItem(pointNumber);

        if (savedPosition) {
            const position = JSON.parse(savedPosition);
            point.style.left = position.left;
            point.style.top = position.top;
        }

        // ทำให้ลากจุดได้
        point.setAttribute('draggable', false);
    });

    // เปิด/ปิดโหมดแก้ไข
    editButton.addEventListener('click', function () {
        editMode = !editMode;
        this.textContent = editMode ? 'ยกเลิกการแก้ไข' : 'แก้ไขแผนที่';
        mapEditor.classList.toggle('edit-mode');

        // เปิดใช้งานการลากเมื่ออยู่ในโหมดแก้ไข
        if (editMode) {
            mapPoints.forEach(point => {
                point.setAttribute('draggable', true);
                point.addEventListener('mousedown', startDrag);
            });
        } else {
            mapPoints.forEach(point => {
                point.removeAttribute('draggable');
                point.removeEventListener('mousedown', startDrag);
            });
        }
    });

    function startDrag(e) {
        if (!editMode) return;

        selectedPoint = e.target;
        const rect = mapEditor.getBoundingClientRect();

        function movePoint(event) {
            let x = event.clientX - rect.left;
            let y = event.clientY - rect.top;

            // คำนวณตำแหน่งเป็นเปอร์เซ็นต์
            let percentX = (x / rect.width) * 100;
            let percentY = (y / rect.height) * 100;

            // จำกัดให้อยู่ภายในขอบเขตของแผนที่
            percentX = Math.max(0, Math.min(100, percentX));
            percentY = Math.max(0, Math.min(100, percentY));

            selectedPoint.style.left = percentX + "%";
            selectedPoint.style.top = percentY + "%";
        }

        function stopDrag() {
            document.removeEventListener('mousemove', movePoint);
            document.removeEventListener('mouseup', stopDrag);
        }

        document.addEventListener('mousemove', movePoint);
        document.addEventListener('mouseup', stopDrag);
    }

    // ปุ่ม "บันทึกการเปลี่ยนแปลง" จะเก็บค่าลง localStorage
    saveChanges.addEventListener('click', function () {
        mapPoints.forEach(point => {
            const pointNumber = point.classList[1]; // เช่น "point1"
            const position = {
                left: point.style.left,
                top: point.style.top
            };
            localStorage.setItem(pointNumber, JSON.stringify(position));
        });
    });
});
</script>
<!-- แผนที่อัปเดต -->
</body>

</html>