public class MoveZeroes {
    public static void moveZeroes(int[] nums) {
        int index = 0; // Pointer to place the next non-zero element

        // Step 1: Move all non-zero elements to the front
        for (int i = 0; i < nums.length; i++) {
            if (nums[i] != 0) {
                nums[index] = nums[i];
                index++;
            }
        }

        // Step 2: Fill the rest of the array with zeros
        while (index < nums.length) {
            nums[index] = 0;
            index++;
        }
    }

    public static void main(String[] args) {
        int[] nums1 = {0, 1, 4, 0, 5, 2};
        moveZeroes(nums1);
        System.out.print("Output 1: ");
        for (int n : nums1) {
            System.out.print(n + " ");
        }

        System.out.println();

        int[] nums2 = {0, 0, 0, 1, 3, -2};
        moveZeroes(nums2);
        System.out.print("Output 2: ");
        for (int n : nums2) {
            System.out.print(n + " ");
        }
    }
}
