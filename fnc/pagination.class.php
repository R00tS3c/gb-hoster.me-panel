<?php
/**
 * Pagination v1.1
 * Pagination class used on GameTracker.rs
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * For questions, help, comments, discussion, etc., you can contribute
 * to GitHub - https://github.com/turshija/PHP-Simple-Pagination
 *
 * @link https://github.com/turshija/PHP-Simple-Pagination
 * @copyright 2014 Turshija.com
 * @author Boris Vujicic - turshija@gmail.com
 * @version 1.1
 */
class Pagination {
    /**
     * Function calculates and returns all the possible pages based on several factors:
     * number of elements, pagination width, elements per page etc
     * @param $numberOfData int - Number of all data that is going in pagination
     * @param $current int - Current page
     * @param $perPage int - Number of items that will be shown per page
     * @param $width int - Pagination width, shouldn't be lower than 7 but will still work
     * @return array - Array of data needed to make pagination: next/prev pages and all page numbers
     * @author Boris Vujicic - turshija@gmail.com
     */
    function load($numberOfData, $current, $perPage=10, $width = 9) {
        // Count number of pages
        $numberOfPages = ceil( $numberOfData / $perPage );

        // Make checks about current page (dont allow negative, non integer, larger then max etc)
        $current = (int)$current;
        if ($current < 1) { 
            $current = 1;
        } else if ($current > $numberOfPages) {
            $current = $numberOfPages;
        }

        $ret['currentPage'] = $current;

        // set next and previous buttons
        if ($current <= 1) { 
            $ret['previousEnabled'] = 0;
        } else { 
            $ret['previousEnabled'] = 1;
        }

        if ($current >= $numberOfPages) { 
            $ret['nextEnabled'] = 0;
        }

        else { 
            $ret['nextEnabled'] = 1;
        }

        // if number of pages is less then (or equal) to width, return all numbers
        if ( $numberOfPages <= $width ) {
            for ($i = 1; $i <= $numberOfPages; $i++) {
                $ret['numbers'][] = $i;
            }

        // else return numbers splitted with ".."
        } else {
            $halfWidth = floor($width/2);

            // We have 3 cases
            // First case is when current page is smaller then halfWidth+1
            // Pagination should look like this -> 1,2,3,4 .. 10
            if ($current <= $halfWidth+1) {
                // Add all numbers less than current
                for ($i = 1; $i < $current; $i++) {
                    $ret['numbers'][] = $i;
                }

                // Add current number
                $ret['numbers'][] = $current;

                // Add all numbers after current
                for ($i = 1; $i <= $halfWidth+2-$current;$i++) {
                    $ret['numbers'][] = ($i+$current);
                }

                // Add ".." and then last page number
                $ret['numbers'][] = "..";
                $ret['numbers'][] = $numberOfPages;

            // Second case is when (number of pages - halfWidth -1) is larger then current
            // Pagination should look like this -> 1 .. 7,8,9,10
            } else if ($current > $numberOfPages - $halfWidth - 1) {
                // Add first page number, and then ".."
                $ret['numbers'][] = "1";
                $ret['numbers'][] = "..";

                // Add all numbers between ".." and current page
                for ($i = ($numberOfPages-$halfWidth-1); $i<$current; $i++) {
                    $ret['numbers'][] = $i;
                }

                // Add current page number
                $ret['numbers'][] = $current;

                // Add all page numbers after current page
                for ($i = $current+1; $i<=$numberOfPages; $i++) {
                    $ret['numbers'][] = $i;
                }

            // Third case is when current page is in the middle, and far away from first and last page
            // Pagination should look like this -> 1 .. 4,5,6 .. 10
            } else {
                // Add first page number, and then ".."
                $ret['numbers'][] = "1";
                $ret['numbers'][] = "..";

                // Add all numbers between first ".." and current page
                for ($i=($current-$halfWidth+2);$i<=($current-1);$i++) {
                    $ret['numbers'][] = $i;
                }

                // Add current page
                $ret['numbers'][] = $current;

                // Add all numbers between current page and second ".."
                for ($i=$current+1;$i<=$current+$halfWidth-2;$i++) {
                    $ret['numbers'][] = $i;
                }

                // Add ".." and then last page number
                $ret['numbers'][] = "..";
                $ret['numbers'][] = $numberOfPages;
            }
        }

        return $ret;

    }
}
?>