@ -23,15 +23,13 @@ if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $position = $_POST['position'];
        $number = $_POST['number'];
        $image_sequence = $_POST['image_sequence'];
        $linkedin = $_POST['linkedin'];
        $email = $_POST['email'];
        $image_name = $_FILES['image']['name'];
        $image_type = $_FILES['image']['type'];
        $image_size = $_FILES['image']['size'];
        $image_data = file_get_contents($_FILES['image']['tmp_name']);

        $stmt = $pdo->prepare("INSERT INTO meet_our_team (name, description, position, phone, image_sequence, linkedin, email, image_name, image_type, image_size, image_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $position, $number, $image_sequence, $linkedin, $email, $image_name, $image_type, $image_size, $image_data]);
        $stmt = $pdo->prepare("INSERT INTO meet_our_team (name, description, position, phone, image_sequence, image_name, image_type, image_size, image_data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $position, $number, $image_sequence, $image_name, $image_type, $image_size, $image_data]);
        $success = "Team member added successfully!";
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
@ -40,19 +38,17 @@ if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $position = $_POST['position'];
        $number = $_POST['number'];
        $image_sequence = $_POST['image_sequence'];
        $linkedin = $_POST['linkedin'];
        $email = $_POST['email'];

        if ($_FILES['image']['name']) {
            $image_name = $_FILES['image']['name'];
            $image_type = $_FILES['image']['type'];
            $image_size = $_FILES['image']['size'];
            $image_data = file_get_contents($_FILES['image']['tmp_name']);
            $stmt = $pdo->prepare("UPDATE meet_our_team SET name = ?, description = ?, position = ?, phone = ?, image_sequence = ?, linkedin = ?, email = ?, image_name = ?, image_type = ?, image_size = ?, image_data = ? WHERE id = ?");
            $stmt->execute([$name, $description, $position, $number, $image_sequence, $linkedin, $email, $image_name, $image_type, $image_size, $image_data, $id]);
            $stmt = $pdo->prepare("UPDATE meet_our_team SET name = ?, description = ?, position = ?, phone = ?, image_sequence = ?, image_name = ?, image_type = ?, image_size = ?, image_data = ? WHERE id = ?");
            $stmt->execute([$name, $description, $position, $number, $image_sequence, $image_name, $image_type, $image_size, $image_data, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE meet_our_team SET name = ?, description = ?, position = ?, phone = ?, image_sequence = ?, linkedin = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $description, $position, $number, $image_sequence, $linkedin, $email, $id]);
            $stmt = $pdo->prepare("UPDATE meet_our_team SET name = ?, description = ?, position = ?, phone = ?, image_sequence = ? WHERE id = ?");
            $stmt->execute([$name, $description, $position, $number, $image_sequence, $id]);
        }
        $success = "Team member updated successfully!";
    } elseif (isset($_POST['delete'])) {
@ -121,14 +117,6 @@ if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        <label class="block text-sm font-medium text-purple-900">Image Sequence</label>
                        <input type="number" name="image_sequence" required class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">LinkedIn</label>
                        <input type="text" name="linkedin" class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Email</label>
                        <input type="email" name="email" class="w-full px-4 py-2 border border-purple-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-purple-900">Image</label>
                        <input type="file" name="image" accept="image/*" required class="w-full px-4 py-2">
@ -158,9 +146,6 @@ if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                <td class="p-2 border"><?= $index + 1 ?></td>
                                <td class="p-2 border">
                                    <div class="font-medium"><?= htmlspecialchars($member['name']) ?></div>
                                    <?php if ($member['email']): ?>
                                        <div class="text-xs text-gray-500"><?= htmlspecialchars($member['email']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-2 border"><?= htmlspecialchars($member['position']) ?></td>
                                <td class="p-2 border"><?= htmlspecialchars($member['phone']) ?></td>
@ -182,8 +167,6 @@ if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                        <input type="text" name="position" value="<?= htmlspecialchars($member['position']) ?>" class="border p-2 rounded" placeholder="Position" required />
                                        <input type="text" name="number" value="<?= htmlspecialchars($member['phone']) ?>" class="border p-2 rounded" placeholder="Phone Number" />
                                        <input type="number" name="image_sequence" value="<?= $member['image_sequence'] ?>" class="border p-2 rounded" placeholder="Display Order" required />
                                        <input type="url" name="linkedin" value="<?= htmlspecialchars($member['linkedin']) ?>" class="border p-2 rounded" placeholder="LinkedIn URL" />
                                        <input type="email" name="email" value="<?= htmlspecialchars($member['email']) ?>" class="border p-2 rounded" placeholder="Email" />
                                        <div class="col-span-2">
                                            <textarea name="description" rows="3" class="w-full border p-2 rounded" placeholder="Description"><?= htmlspecialchars($member['description']) ?></textarea>
                                        </div>
