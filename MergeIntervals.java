import java.util.*;

public class MergeIntervals {
    public static int[][] merge(int[][] intervals) {
        Arrays.sort(intervals, (a, b) -> a[0] - b[0]);
        List<int[]> result = new ArrayList<>();
        int[] current = intervals[0];
        for (int i = 1; i < intervals.length; i++) {
            if (intervals[i][0] <= current[1]) {
                current[1] = Math.max(current[1], intervals[i][1]);
            } else {
                result.add(current);
                current = intervals[i];
            }
        }
        result.add(current);
        return result.toArray(new int[result.size()][]);
    }

    public static void main(String[] args) {
        int[][] intervals1 = {{1,5},{3,6},{8,10},{15,18}};
        int[][] res1 = merge(intervals1);
        for (int[] interval : res1) System.out.println(Arrays.toString(interval));

        int[][] intervals2 = {{5,7},{1,3},{4,6},{8,10}};
        int[][] res2 = merge(intervals2);
        for (int[] interval : res2) System.out.println(Arrays.toString(interval));
    }
}
