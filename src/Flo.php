<?php

declare(strict_types=1);

namespace Dreadnip\Flo;

use Symfony\Component\HttpClient\CurlHttpClient;

class Flo
{
    private CurlHttpClient $client;

    private string $profileUrl = 'https://apps.runescape.com/runemetrics/profile/profile?user=%s&activities=20';

    private string $clanUrl = 'http://services.runescape.com/m=clan-hiscores/members_lite.ws?clanName=%s';

    private string $detailUrl = 'http://services.runescape.com/m=website-data/playerDetails.ws?membership=true&names=%s&callback=angular.callbacks._0';

    public function __construct()
    {
        $this->client = new CurlHttpClient();
    }

    public function getProfile(string $player): array
    {
        $response = $this->client->request('GET', sprintf($this->profileUrl, $this->clean($player)));
        return $response->toArray();
    }

    public function getDetails(array $players): array
    {
        $players = $this->cleanList($players);

        if (count($players) > 99) {
            $responses = [];
            $chunkedResults = [];
            $chunked = array_chunk($players, 99, false);

            foreach ($chunked as $chunk) {
                $responses[] = $this->client->request('GET', sprintf($this->detailUrl, json_encode($chunk)));
            }

            foreach ($responses as $response) {
                $chunkedResults[] = $this->trim($response->getContent());
            }

            return array_merge([], ...$chunkedResults);
        }

        $response = $this->client->request('GET', sprintf($this->detailUrl, json_encode($chunk)));
        return $this->trim($response->getContent());
    }

    public function getProfiles(array $players): array
    {
        $profiles = [];
        $responses = [];

        foreach ($players as $player) {
            $responses[] = $this->client->request('GET', sprintf($this->profileUrl, $this->clean($player)));
        }

        foreach ($this->client->stream($responses) as $response => $chunk) {
            if ($chunk->isLast()) {
                $profiles[] = $response->getContent();
            }
        }

        return $profiles;
    }

    public function getClanList(string $clan, bool $onlyNames = false): ?array
    {
        $response = $this->client->request('GET', sprintf($this->clanUrl, $this->clean($clan)));

        if ($response->getHeaders()['content-type'][0] !== 'text/comma-separated-values') {
            return null;
        }

        $clanListCsv = $response->getContent();

        $clanList = [];
        $csvRows = explode("\n", $clanListCsv);

        foreach ($csvRows as $key => $row) {
            if ($key === 0 || $key >= count($csvRows) - 1) {
                continue;
            }

            $columns = explode(',', $row);

            if ($onlyNames === true) {
                $clanList[] = $this->clean(utf8_encode($columns[0]));
            } else {
                $clanList[] = [
                    'name' => $this->clean(utf8_encode($columns[0])),
                    'rank' => $columns[1],
                    'clan_xp' => $columns[2],
                    'clan_kills' => $columns[3],
                ];
            }
        }

        return $clanList;
    }

    private function clean(string $input): string
    {
        return \preg_replace('/[^A-Za-z0-9]++/', '_', strtolower($input));
    }

    private function cleanList(array $list): array
    {
        return array_map(fn($name) => $this->clean($name), $list);
    }

    private function trim(string $angularObject): array
    {
        /*
         * The only way we can get data from the details endpoint is by wrapping it
         * in this weird Angular callback. To get the actual data we have to trim
         * the returned JS object from the array.
         */
        return json_decode(substr($angularObject, 21, -3), true);
    }
}
