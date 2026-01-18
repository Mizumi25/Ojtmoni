<div class="p-4">
    <h2 class="text-lg font-semibold mb-4">Edit User</h2>

    <form>
        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Name</label>
            <input type="text" class="w-full p-2 border rounded-md" value="John Doe">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Email</label>
            <input type="email" class="w-full p-2 border rounded-md" value="john@example.com">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Role</label>
            <select class="w-full p-2 border rounded-md">
                <option>Admin</option>
                <option>Student</option>
                <option>Staff</option>
            </select>
        </div>

        <div class="flex justify-end space-x-2">
            <button @click="openEdit = false" type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Save Changes</button>
        </div>
    </form>
</div>
