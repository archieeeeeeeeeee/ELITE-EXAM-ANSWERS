
---MYSQL QUERIES FOR ALBUM SALES DATABASE---

-- 1. Displays the total number of albums sold by each artist.
--    It counts the number of album records for each artist and sorts the
--    result by the artist's name.
SELECT
    artist_name,
    COUNT(album_name) AS total_albums
FROM
    album_sales
GROUP BY
    artist_name
ORDER BY
    artist_name ASC;


-- 2. Displays the combined (total) album sales for each artist.
--    It calculates the sum of the `sales` column for all albums belonging
--    to an artist and orders the results to show the highest-selling artists first.
SELECT
    artist_name,
    SUM(sales) AS combined_sales
FROM
    album_sales
GROUP BY
    artist_name
ORDER BY
    combined_sales DESC;


-- 3. Displays the top 1 artist who had the most combined album sales.
--    This builds on the previous query by using `LIMIT 1` to return only
--    the single top result after ordering by combined sales.
SELECT
    artist_name,
    SUM(sales) AS combined_sales
FROM
    album_sales
GROUP BY
    artist_name
ORDER BY
    combined_sales DESC
LIMIT 1;


-- 4. Displays the top 10 best-selling albums for each year.
--    This query uses a Common Table Expression (WITH clause) to first assign a rank
--    to each album based on its sales within a given year. It then selects
--    only the albums with a rank from 1 to 10.
WITH RankedAlbums AS (
    SELECT
        artist_name,
        album_name,
        sales,
        YEAR(date_released) AS release_year,
        ROW_NUMBER() OVER(PARTITION BY YEAR(date_released) ORDER BY sales DESC) as sales_rank
    FROM
        album_sales
)
SELECT
    release_year,
    artist_name,
    album_name,
    sales
FROM
    RankedAlbums
WHERE
    sales_rank <= 10
ORDER BY
    release_year DESC,
    sales_rank ASC;


-- 5. Displays a list of all albums for a specific searched artist.
--    This query uses a simple `WHERE` clause to filter the results and show all
--    album details for a single artist. You can change 'IVE' to any other
--    artist's name to search for their albums.
SELECT
    artist_name,
    album_name,
    sales,
    date_released
FROM
    album_sales
WHERE
    artist_name = 'IVE';