<?php

namespace App\Jobs;

use App\Models\WorkoutMedia;
use App\Services\VideoService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TranscodeVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) { // If video processing failed
            Log::error('[QUEUE] Max try attempted while perfoming video processing for video with id: ' . $this->videoInfo['id'] . ' at ' . Carbon::now());
            $this->delete();

            // Move faild file to S3
            $video = file_get_contents(Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path($this->videoInfo['video_url']));

            Log::info('Queue:: File Move Start on S3 ...');
            try{
                $moveTo = Storage::disk('s3')->put($this->videoInfo['video_url'], (string) $video, 'private');
                Log::info('Move: '.$moveTo);
            // } catch (\Aws\S3\Exception\S3Exception $e){
            //     Log::info('Queue:: Move Error : '.$e->getMessage());
            // } catch (\Aws\Exception\AwsException $e){
            //     Log::info('Queue:: Move Error : '.$e->getMessage());
            } catch (\Exception $e){
                Log::info('Queue:: Move Error : '.$e->getMessage());
            }

            if ($moveTo){
                Log::info('Queue:: File Move Successfully on public');
                Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->delete($this->videoInfo['video_url']);
            }

        } else {
            try {
                Log::info('[QUEUE] Video processing started for Video id: ' . $this->videoInfo['id'] . ' at ' . Carbon::now());

                // Updated transcoding status to in-queue
                WorkoutMedia::where('id', $this->videoInfo['id'])
                    ->update([
                        'is_transacoded' => Config::get('constant.TRANSCODING_IN_QUEUE_VIDEO_STATUS'),
                    ]);

                // Transcode video - Start
                $videoService = new VideoService();

                $response = $videoService->transcode($this->videoInfo);

                if ($response['status'] === 0) {
                    $this->isFailed($this->videoInfo, $response['message']);

                    return false;
                }
                // Transcode video - Ends
                Log::info($response);
                // Move Transcoded file to public
                $video = file_get_contents(Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path($response['video_url']));

                Log::info('Queue:: File Move Start on public ...');
                try{
                    $moveTo = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->put($response['video_url'], (string) $video, 'private');
                    Log::info('Move: '.$moveTo);
                // } catch (\Aws\S3\Exception\S3Exception $e){
                //     Log::info('Queue:: Move Error : '.$e->getMessage());
                // } catch (\Aws\Exception\AwsException $e){
                //     Log::info('Queue:: Move Error : '.$e->getMessage());
                } catch (\Exception $e){
                    Log::info('Queue:: Move Error : '.$e->getMessage());
                }

                if ($moveTo){
                    Log::info('Queue:: File Move Successfully on public');
                    Storage::disk('public')->delete($response['video_url']);
                }

                // Saving video
                $dataSave = [
                    'is_transacoded' => Config::get('constant.TRANSCODING_DONE_VIDEO_STATUS'),
                    'size' => $response['file_size'],
                    'path' => $response['file_name'],
                ];

                // Updated video data
                WorkoutMedia::where('id', $this->videoInfo['id'])->update($dataSave);

                // Delete queue
                $this->delete();

                Log::info("[QUEUE] Temp video unlink - start for Video id: {$this->videoInfo['id']} at " . Carbon::now());
                // Delete temp video from directory
                Storage::disk('public')->delete($this->videoInfo['video_url']);

                Log::info('[QUEUE] Video processing ended for Video id: ' . $this->videoInfo['id'] . ' at ' . Carbon::now());
            } catch (\Exception $e) {
                Log::error("[QUEUE] Error while perfoming video processing for video with id: {$this->videoInfo['id']} at " . Carbon::now() . ". Error: {$e->getMessage()}");
                $this->isFailed($this->videoInfo, $e);
            }
        }
    }

    /**
     * Failed
     * @param array $data
     * @param string $message
     */
    private function isFailed($data, $message)
    {
        $dataSave = [
            'is_transacoded' => Config::get('constant.TRANSCODING_FAILED_VIDEO_STATUS'),
        ];

        // Make transcoded status = failed if there is error or video not transcoded
        WorkoutMedia::where('id', $data['id'])->update($dataSave);
        Log::error("[QUEUE] Video transcoding status updated to failed for video ID: {$data['id']}");
    }
}
