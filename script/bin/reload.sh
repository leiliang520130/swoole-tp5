echo "loading"
pid=`pidof live_mast`
echo $pid
kill -USR1 $pid
echo "loading success"