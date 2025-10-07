import java.util.Arrays;

public class RotateArrayKSteps{
    public static void main(String[] args) {
        int[] nums = {3, 4, 1, 5, 3, -5};
        int k = 8;

        int n = nums.length;
        if (n == 0) {
            System.out.println(Arrays.toString(nums));
            return;
        }

        k = k % n; // Handle case when k > n

        reverse(nums, 0, k - 1);
        reverse(nums, k, n - 1);
        reverse(nums, 0, n - 1);

        System.out.println(Arrays.toString(nums));
    }

    private static void reverse(int[] arr, int left, int right) {
        while (left < right) {
            int temp = arr[left];
            arr[left] = arr[right];
            arr[right] = temp;
            left++;
            right--;
        }
    }
}
