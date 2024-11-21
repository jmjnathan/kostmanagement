<?php
// edit-room-handler.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $roomId = $_GET['id']; // Get room ID from URL
   $roomName = $_POST['name'];
   $roomType = $_POST['room_type'];
   $roomAc = $_POST['ac'];
   $roomCapacity = $_POST['capacity'];
   $roomPrice = $_POST['price'];
   $roomStatus = $_POST['status'];

   // Update the room data in the database
   $stmt = $pdo->prepare("UPDATE rooms SET name = :name, type = :type, ac = :ac, capacity = :capacity, price = :price, status = :status WHERE id = :id");
   $stmt->execute([
      'name' => $roomName,
      'type' => $roomType,
      'ac' => $roomAc,
      'capacity' => $roomCapacity,
      'price' => $roomPrice,
      'status' => $roomStatus,
      'id' => $roomId
   ]);

   // Redirect to rooms page after successful update
   header('Location: kamar.php');
   exit();
}
?>
